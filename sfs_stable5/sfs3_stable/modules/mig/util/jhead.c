
/***************************************************************************
 **                                                                       **
 **  jhead.c - Distributed with, but not part of, MiG.                    **
 **            http://tangledhelix.com/software/mig/                      **
 **                                                                       **
 **  This code is the property of Matthias Wandel and can be found at     **
 **  http://www.sentex.net/~mwandel/jhead/index.html                      **
 **                                                                       **
 **  Please see the file docs/Utilities.txt.                              **
 **                                                                       **
 ***************************************************************************/


/* -- below this line is the original code by Matthias Wendel -- */


//--------------------------------------------------------------------------
// Program to pull the information out of various types of EFIF digital 
// camera files and show it in a reasonably consistent way
//
// Version 0.9
//
// Compiles with MSVC on Windows, or with GCC on Linux
//
// Compileing under linux: Must include math library.
// Use: cc -lm -O3 -o jhead jhead.c
//
// Matthias Wandel,  Dec 1999 - April 2000
//--------------------------------------------------------------------------
#include <stdio.h>
#include <stdlib.h>
#include <memory.h>
#include <malloc.h>
#include <math.h>
#include <string.h>
#include <time.h>
#include <sys/stat.h>
#include <ctype.h>

#ifdef _WIN32
    #include <process.h>
    #include <io.h>
    #define stat _stat
#else
    #include <sys/types.h>
    #include <dirent.h>
    #include <unistd.h>
    #include <errno.h>
#endif

typedef unsigned char uchar;

#ifndef TRUE
    #define TRUE 1
    #define FALSE 0
#endif

//--------------------------------------------------------------------------
// This structure stores Exif header image elements in a simple manner
// Used to store camera data as extracted from the various ways that it can be
// stored in a nexif header
typedef struct {
    char  FileName     [120];
    time_t FileDateTime;
    unsigned FileSize;
    char  CameraMake   [32];
    char  CameraModel  [40];
    char  DateTime     [20];
    int   Height, Width;
    int   IsColor;
    int   FlashUsed;
    float FocalLength;
    float ExposureTime;
    float ApertureFNumber;
    float Distance;
    float CCDWidth;
    char  Comments[200];
}ImageInfoType;

ImageInfoType ImageInfo;

//--------------------------------------------------------------------------
// This structure is used to store a section of a Jpeg file.
typedef struct {
    uchar *  Data;
    int      Type;
    unsigned Size;
}Section_t;

Section_t Sections[20];
int SectionsRead;
int HaveAll;
char * LastExifRefd;

double FocalplaneXRes;
double FocalplaneUnits;
int ExifImageWidth;

int FilesMatched;

int remove_thumbnails = FALSE;
int DoSubdirs = FALSE;

char * CurrentFile;

//--------------------------------------------------------------------------

int ShowTags     = 0;        // Do not show raw by default.
int ShowStruct   = 1;        // Show the built structure by default.
int MotorolaOrder = 0;

#define EXIT_FAILURE  1
#define EXIT_SUCCESS  0

//--------------------------------------------------------------------------
// These macros are used to read the input file.
// To reuse this code in another application, you might need to change these.
//--------------------------------------------------------------------------

// Error exit handler
//#define ERREXIT(msg)  (fprintf(stderr,"Error : %s\n", msg), exit(EXIT_FAILURE))
void ERREXIT(char * msg)
{
    fprintf(stderr,"Error : %s\n", msg);
    fprintf(stderr,"in file '%s'\n",CurrentFile);
    exit(EXIT_FAILURE);
} 

//--------------------------------------------------------------------------
// JPEG markers consist of one or more 0xFF bytes, followed by a marker
// code byte (which is not an FF).  Here are the marker codes of interest
// in this program.  (See jdmarker.c for a more complete list.)
//--------------------------------------------------------------------------
 
#define M_SOF0  0xC0            // Start Of Frame N
#define M_SOF1  0xC1            // N indicates which compression process
#define M_SOF2  0xC2            // Only SOF0-SOF2 are now in common use
#define M_SOF3  0xC3
#define M_SOF5  0xC5            // NB: codes C4 and CC are NOT SOF markers
#define M_SOF6  0xC6
#define M_SOF7  0xC7
#define M_SOF9  0xC9
#define M_SOF10 0xCA
#define M_SOF11 0xCB
#define M_SOF13 0xCD
#define M_SOF14 0xCE
#define M_SOF15 0xCF
#define M_SOI   0xD8            // Start Of Image (beginning of datastream)
#define M_EOI   0xD9            // End Of Image (end of datastream)
#define M_SOS   0xDA            // Start Of Scan (begins compressed data)
#define M_EXIF  0xE1            
#define M_COM   0xFE            // COMment 


#define PSEUDO_IMAGE_MARKER 0x123; // Extra value.
//--------------------------------------------------------------------------
// Get 16 bits motorola order (always) for jpeg header stuff.
//--------------------------------------------------------------------------
static int Get16m(void * Short)
{
    return (((uchar *)Short)[0] << 8) | ((uchar *)Short)[1];
}


//--------------------------------------------------------------------------
// Process a COM marker.
// We want to print out the marker contents as legible text;
// we must guard against random junk and varying newline representations.
//--------------------------------------------------------------------------
static void process_COM (uchar * Data, int length)
{
    int ch;
    char Comment[250];
    int nch;
    int a;

    nch = 0;

    if (length > 200) length = 200; // Truncate if it won't fit in our structure.

    for (a=2;a<length;a++){
        ch = Data[a];

        if (ch == '\r' && Data[a+1] == '\n') continue; // Remove cr followed by lf.

        if (isprint(ch) || ch == '\n' || ch == '\t'){
            Comment[nch++] = (char)ch;
        }else{
            Comment[nch++] = '?';
        }
    }

    Comment[nch] = '\0'; // Null terminate

    if (ShowTags){
        printf("COM marker comment: %s\n",Comment);
    }

    strcpy(ImageInfo.Comments,Comment);
}

 
//--------------------------------------------------------------------------
// Process a SOFn marker.  This is useful for the image dimensions
//--------------------------------------------------------------------------
static void process_SOFn (uchar * Data, int marker)
{
    int data_precision, num_components;
    const char * process;

    data_precision = Data[2];
    ImageInfo.Height = Get16m(Data+3);
    ImageInfo.Width = Get16m(Data+5);
    num_components = Data[7];

    if (num_components == 3){
        ImageInfo.IsColor = 1;
    }else{
        ImageInfo.IsColor = 0;
    }

    switch (marker) {
        case M_SOF0:  process = "Baseline";  break;
        case M_SOF1:  process = "Extended sequential";  break;
        case M_SOF2:  process = "Progressive";  break;
        case M_SOF3:  process = "Lossless";  break;
        case M_SOF5:  process = "Differential sequential";  break;
        case M_SOF6:  process = "Differential progressive";  break;
        case M_SOF7:  process = "Differential lossless";  break;
        case M_SOF9:  process = "Extended sequential, arithmetic coding";  break;
        case M_SOF10: process = "Progressive, arithmetic coding";  break;
        case M_SOF11: process = "Lossless, arithmetic coding";  break;
        case M_SOF13: process = "Differential sequential, arithmetic coding";  break;
        case M_SOF14: process = "Differential progressive, arithmetic coding"; break;
        case M_SOF15: process = "Differential lossless, arithmetic coding";  break;
        default:      process = "Unknown";  break;
    }

    if (ShowTags){
        printf("JPEG image is %uw * %uh, %d color components, %d bits per sample\n",
                   ImageInfo.Width, ImageInfo.Height, num_components, data_precision);
        printf("JPEG process: %s\n", process);
    }
}

//--------------------------------------------------------------------------
// Describes format descriptor
static int BytesPerFormat[] = {0,1,1,2,4,8,1,1,2,4,8,4,8};
#define NUM_FORMATS 12

#define FMT_BYTE       1 
#define FMT_STRING     2
#define FMT_USHORT     3
#define FMT_ULONG      4
#define FMT_URATIONAL  5
#define FMT_SBYTE      6
#define FMT_UNDEFINED  7
#define FMT_SSHORT     8
#define FMT_SLONG      9
#define FMT_SRATIONAL 10
#define FMT_SINGLE    11
#define FMT_DOUBLE    12

//--------------------------------------------------------------------------
// Describes tag values

#define TAG_EXIF_OFFSET       0x8769
#define TAG_INTEROP_OFFSET    0xa005

#define TAG_MAKE              0x010F
#define TAG_MODEL             0x0110

#define TAG_EXPOSURETIME      0x829A
#define TAG_FNUMBER           0x829D

#define TAG_SHUTTERSPEED      0x9201
#define TAG_APERTURE          0x9202
#define TAG_MAXAPERTURE       0x9205
#define TAG_FOCALLENGTH       0x920A

#define TAG_DATETIME_ORIGINAL 0x9003
#define TAG_USERCOMMENT       0x9286

#define TAG_SUBJECT_DISTANCE  0x9206
#define TAG_LIGHT_SOURCE      0x9208
#define TAG_FLASH             0x9209

#define TAG_FOCALPLANEXRES    0xa20E
#define TAG_FOCALPLANEUNITS   0xa210
#define TAG_IMAGEWIDTH        0xA002


static const struct {
    unsigned short Tag;
    char * Desc;
}TagTable[] = {
  {	0x100,	"ImageWidth"},
  {	0x101,	"ImageLength"},
  {	0x102,	"BitsPerSample"},
  {	0x103,	"Compression"},
  {	0x106,	"PhotometricInterpretation"},
  {	0x10A,	"FillOrder"},
  {	0x10D,	"DocumentName"},
  {	0x10E,	"ImageDescription"},
  {	0x10F,	"Make"},
  {	0x110,	"Model"},
  {	0x111,	"StripOffsets"},
  {	0x112,	"Orientation"},
  {	0x115,	"SamplesPerPixel"},
  {	0x116,	"RowsPerStrip"},
  {	0x117,	"StripByteCounts"},
  {	0x11A,	"XResolution"},
  {	0x11B,	"YResolution"},
  {	0x11C,	"PlanarConfiguration"},
  {	0x128,	"ResolutionUnit"},
  {	0x12D,	"TransferFunction"},
  {	0x131,	"Software"},
  {	0x132,	"DateTime"},
  {	0x13B,	"Artist"},
  {	0x13E,	"WhitePoint"},
  {	0x13F,	"PrimaryChromaticities"},
  {	0x156,	"TransferRange"},
  {	0x200,	"JPEGProc"},
  {	0x201,	"JPEGInterchangeFormat"},
  {	0x202,	"JPEGInterchangeFormatLength"},
  {	0x211,	"YCbCrCoefficients"},
  {	0x212,	"YCbCrSubSampling"},
  {	0x213,	"YCbCrPositioning"},
  {	0x214,	"ReferenceBlackWhite"},
  {	0x828D,	"CFARepeatPatternDim"},
  {	0x828E,	"CFAPattern"},
  {	0x828F,	"BatteryLevel"},
  {	0x8298,	"Copyright"},
  {	0x829A,	"ExposureTime"},
  {	0x829D,	"FNumber"},
  {	0x83BB,	"IPTC/NAA"},
  {	0x8769,	"ExifOffset"},
  {	0x8773,	"InterColorProfile"},
  {	0x8822,	"ExposureProgram"},
  {	0x8824,	"SpectralSensitivity"},
  {	0x8825,	"GPSInfo"},
  {	0x8827,	"ISOSpeedRatings"},
  {	0x8828,	"OECF"},
  {	0x9000,	"ExifVersion"},
  {	0x9003,	"DateTimeOriginal"},
  {	0x9004,	"DateTimeDigitized"},
  {	0x9101,	"ComponentsConfiguration"},
  {	0x9102,	"CompressedBitsPerPixel"},
  {	0x9201,	"ShutterSpeedValue"},
  {	0x9202,	"ApertureValue"},
  {	0x9203,	"BrightnessValue"},
  {	0x9204,	"ExposureBiasValue"},
  {	0x9205,	"MaxApertureValue"},
  {	0x9206,	"SubjectDistance"},
  {	0x9207,	"MeteringMode"},
  {	0x9208,	"LightSource"},
  {	0x9209,	"Flash"},
  {	0x920A,	"FocalLength"},
  {	0x927C,	"MakerNote"},
  {	0x9286,	"UserComment"},
  {	0x9290,	"SubSecTime"},
  {	0x9291,	"SubSecTimeOriginal"},
  {	0x9292,	"SubSecTimeDigitized"},
  {	0xA000,	"FlashPixVersion"},
  {	0xA001,	"ColorSpace"},
  {	0xA002,	"ExifImageWidth"},
  {	0xA003,	"ExifImageLength"},
  {	0xA005,	"InteroperabilityOffset"},
  {	0xA20B,	"FlashEnergy"},			        // 0x920B in TIFF/EP
  {	0xA20C,	"SpatialFrequencyResponse"},	// 0x920C    -  -
  {	0xA20E,	"FocalPlaneXResolution"},    	// 0x920E    -  -
  {	0xA20F,	"FocalPlaneYResolution"},	    // 0x920F    -  -
  {	0xA210,	"FocalPlaneResolutionUnit"},	// 0x9210    -  -
  {	0xA214,	"SubjectLocation"},		        // 0x9214    -  -
  {	0xA215,	"ExposureIndex"},		        // 0x9215    -  -
  {	0xA217,	"SensingMethod"},		        // 0x9217    -  -
  {	0xA300,	"FileSource"},
  {	0xA301,	"SceneType"},
  {      0, NULL}
} ;



//--------------------------------------------------------------------------
// Convert a 16 bit unsigned value from file's native byte order
//--------------------------------------------------------------------------
static int Get16u(void * Short)
{
    if (MotorolaOrder){
        return (((uchar *)Short)[0] << 8) | ((uchar *)Short)[1];
    }else{
        return (((uchar *)Short)[1] << 8) | ((uchar *)Short)[0];
    }
}

//--------------------------------------------------------------------------
// Convert a 32 bit signed value from file's native byte order
//--------------------------------------------------------------------------
static int Get32s(void * Long)
{
    if (MotorolaOrder){
        return  ((( char *)Long)[0] << 24) | (((uchar *)Long)[1] << 16)
              | (((uchar *)Long)[2] << 8 ) | (((uchar *)Long)[3] << 0 );
    }else{
        return  ((( char *)Long)[3] << 24) | (((uchar *)Long)[2] << 16)
              | (((uchar *)Long)[1] << 8 ) | (((uchar *)Long)[0] << 0 );
    }
}

//--------------------------------------------------------------------------
// Convert a 32 bit unsigned value from file's native byte order
//--------------------------------------------------------------------------
static unsigned Get32u(void * Long)
{
    return (unsigned)Get32s(Long) & 0xffffffff;
}

//--------------------------------------------------------------------------
// Display a number as one of its many formats
//--------------------------------------------------------------------------
static void PrintFormatNumber(void * ValuePtr, int Format)
{
    switch(Format){
        case FMT_SBYTE:
        case FMT_BYTE:      printf("%02x ",*(uchar *)ValuePtr);             break;
        case FMT_USHORT:    printf("%d\n",Get16u(ValuePtr));                break;
        case FMT_ULONG:     
        case FMT_SLONG:     printf("%d\n",Get32s(ValuePtr));                break;
        case FMT_SSHORT:    printf("%hd\n",(signed short)Get16u(ValuePtr)); break;
        case FMT_URATIONAL:
        case FMT_SRATIONAL: 
           printf("%d/%d\n",Get32s(ValuePtr), Get32s(4+(char *)ValuePtr)); break;

        case FMT_SINGLE:    printf("%f\n",(double)*(float *)ValuePtr);   break;
        case FMT_DOUBLE:    printf("%f\n",*(double *)ValuePtr);          break;
    }
}


//--------------------------------------------------------------------------
// Evaluate number, be it int, rational, or float from directory.
//--------------------------------------------------------------------------
static double ConvertAnyFormat(void * ValuePtr, int Format)
{
    double Value;
    Value = 0;

    switch(Format){
        case FMT_SBYTE:     Value = *(signed char *)ValuePtr;  break;
        case FMT_BYTE:      Value = *(uchar *)ValuePtr;        break;

        case FMT_USHORT:    Value = Get16u(ValuePtr);          break;
        case FMT_ULONG:     Value = Get32u(ValuePtr);          break;

        case FMT_URATIONAL:
        case FMT_SRATIONAL: 
            {
                int Num,Den;
                Num = Get32s(ValuePtr);
                Den = Get32s(4+(char *)ValuePtr);
                if (Den == 0){
                    Value = 0;
                }else{
                    Value = (double)Num/Den;
                }
                break;
            }

        case FMT_SSHORT:    Value = (signed short)Get16u(ValuePtr);  break;
        case FMT_SLONG:     Value = Get32s(ValuePtr);                break;

        // Not sure if this is correct (never seen float used in Exif format)
        case FMT_SINGLE:    Value = (double)*(float *)ValuePtr;      break;
        case FMT_DOUBLE:    Value = *(double *)ValuePtr;             break;
    }
    return Value;
}

//--------------------------------------------------------------------------
// Process one of the nested EXIF directories.
//--------------------------------------------------------------------------
static void ProcessExifDir(char * DirStart, char * OffsetBase, unsigned ExifLength)
{
    int de;
    int a;
    int NumDirEntries;

    NumDirEntries = Get16u(DirStart);

    if ((DirStart+2+NumDirEntries*12) > (OffsetBase+ExifLength)){
        ERREXIT("Illegally sized directory");
    }

    if (ShowTags){
        printf("Directory with %d entries\n",NumDirEntries);
    }

    for (de=0;de<NumDirEntries;de++){
        int Tag, Format, Components;
        char * ValuePtr;
        int ByteCount;
        char * DirEntry;
        DirEntry = DirStart+2+12*de;

        Tag = Get16u(DirEntry);
        Format = Get16u(DirEntry+2);
        Components = Get32u(DirEntry+4);

        if ((Format-1) >= NUM_FORMATS) {
            // (-1) catches illegal zero case as unsigned underflows to positive large.
            ERREXIT("Illegal format code in EXIF dir");
        }

        ByteCount = Components * BytesPerFormat[Format];

        if (ByteCount > 4){
            unsigned OffsetVal;
            OffsetVal = Get32u(DirEntry+8);
            // If its bigger than 4 bytes, the dir entry contains an offset.
            if (OffsetVal+ByteCount > ExifLength){
                // Bogus pointer offset and / or bytecount value
                printf("Offset %d bytes %d ExifLen %d\n",OffsetVal, ByteCount, ExifLength);

                ERREXIT("Illegal pointer offset value in EXIF");
            }
            ValuePtr = OffsetBase+OffsetVal;
        }else{
            // 4 bytes or less and value is in the dir entry itself
            ValuePtr = DirEntry+8;
        }

        if (LastExifRefd < ValuePtr+ByteCount){
            // Keep track of last byte in the exif header that was actually referenced.
            // That way, we know where the discardable thumbnail data begins.
            LastExifRefd = ValuePtr+ByteCount;
        }

        if (ShowTags){
            // Show tag name
            for (a=0;;a++){
                if (TagTable[a].Tag == 0){
                    printf("  Unknown Tag %04x Value = ", Tag);
                    break;
                }
                if (TagTable[a].Tag == Tag){
                    printf("    %s = ",TagTable[a].Desc);
                    break;
                }
            }

            // Show tag value.
            switch(Format){

                case FMT_UNDEFINED:
                    // Undefined is typically an ascii string.

                case FMT_STRING:
                    // String arrays printed without function call (different from int arrays)
                    printf("\"");
                    for (a=0;a<ByteCount;a++){
                        if (isprint((ValuePtr)[a])){
                            putchar((ValuePtr)[a]);
                        }
                    }
                    printf("\"\n");
                    break;

                default:
                    // Handle arrays of numbers later (will there ever be?)
                    PrintFormatNumber(ValuePtr, Format);
            }
        }

        // Extract useful components of tag
        switch(Tag){

            case TAG_MAKE:
                strncpy(ImageInfo.CameraMake, ValuePtr, 31);
                break;

            case TAG_MODEL:
                strncpy(ImageInfo.CameraModel, ValuePtr, 39);
                break;

            case TAG_DATETIME_ORIGINAL:
                strncpy(ImageInfo.DateTime, ValuePtr, 19);
                break;

            case TAG_USERCOMMENT:
                // Olympus has this padded with trailing spaces.  Remove these first.
                for (a=ByteCount;;){
                    a--;
                    if ((ValuePtr)[a] == ' '){
                        (ValuePtr)[a] = '\0';
                    }else{
                        break;
                    }
                    if (a == 0) break;
                }

                // Copy the comment
                if (memcmp(ValuePtr, "ASCII",5) == 0){
                    for (a=5;a<10;a++){
                        int c;
                        c = (ValuePtr)[a];
                        if (c != '\0' && c != ' '){
                            strncpy(ImageInfo.Comments, a+ValuePtr, 199);
                            break;
                        }
                    }
                    
                }else{
                    strncpy(ImageInfo.Comments, ValuePtr, 199);
                }
                break;

            case TAG_FNUMBER:
                // Simplest way of expressing aperture, so I trust it the most.
                // (overwrite previously computd value if there is one)
                ImageInfo.ApertureFNumber = (float)ConvertAnyFormat(ValuePtr, Format);
                break;

            case TAG_APERTURE:
            case TAG_MAXAPERTURE:
                // More relevant info always comes earlier, so only use this field if we don't 
                // have appropriate aperture information yet.
                if (ImageInfo.ApertureFNumber == 0){
                    ImageInfo.ApertureFNumber 
                        = (float)exp(ConvertAnyFormat(ValuePtr, Format)*log(2)*0.5);
                }
                break;

            case TAG_FOCALLENGTH:
                // Nice digital cameras actually save the focal length as a function
                // of how farthey are zoomed in.
                ImageInfo.FocalLength = (float)ConvertAnyFormat(ValuePtr, Format);
                break;

            case TAG_SUBJECT_DISTANCE:
                // Inidcates the distacne the autofocus camera is focused to.
                // Tends to be less accurate as distance increases.
                ImageInfo.Distance = (float)ConvertAnyFormat(ValuePtr, Format);
                break;

            case TAG_EXPOSURETIME:
                // Simplest way of expressing exposure time, so I trust it most.
                // (overwrite previously computd value if there is one)
                ImageInfo.ExposureTime = (float)ConvertAnyFormat(ValuePtr, Format);
                break;

            case TAG_SHUTTERSPEED:
                // More complicated way of expressing exposure time, so only use
                // this value if we don't already have it from somewhere else.
                if (ImageInfo.ExposureTime == 0){
                    ImageInfo.ExposureTime 
                        = (float)(1/exp(ConvertAnyFormat(ValuePtr, Format)*log(2)));
                }
                break;

            case TAG_FLASH:
                if (ConvertAnyFormat(ValuePtr, Format)){
                    ImageInfo.FlashUsed = 1;
                }
                break;

            case TAG_IMAGEWIDTH:
                ExifImageWidth = (int)ConvertAnyFormat(ValuePtr, Format);
                break;

            case TAG_FOCALPLANEXRES:
                FocalplaneXRes = ConvertAnyFormat(ValuePtr, Format);
                break;

            case TAG_FOCALPLANEUNITS:
                switch((int)ConvertAnyFormat(ValuePtr, Format)){
                    case 1: FocalplaneUnits = 25.4; break; // inch
                    case 2: 
                        // According to the information I was using, 2 measn meters.
                        // But looking at the Cannon powershot's files, inches is the only
                        // sensible value.
                        FocalplaneUnits = 25.4;
                        break;

                    case 3: FocalplaneUnits = 10;   break;  // centimeter
                    case 4: FocalplaneUnits = 1;    break;  // milimeter
                    case 5: FocalplaneUnits = .001; break;  // micrometer
                }
                break;

            case TAG_LIGHT_SOURCE:
                // Rarely set or useful.
                break;
        }

        if (Tag == TAG_EXIF_OFFSET || Tag == TAG_INTEROP_OFFSET){
            char * SubdirStart;
            SubdirStart = OffsetBase + Get32u(ValuePtr);
            if (SubdirStart < OffsetBase || SubdirStart > OffsetBase+ExifLength){
                ERREXIT("Illegal subdirectory link");
            }
            ProcessExifDir(SubdirStart, OffsetBase, ExifLength);
            continue;
        }
    }
}

//--------------------------------------------------------------------------
// Process a EXIF marker
// Describes all the drivel that most digital cameras include...
//--------------------------------------------------------------------------
static void process_EXIF (char * CharBuf, unsigned int length)
{
    ImageInfo.FlashUsed = 0; // If it s from a digicam, and it used flash, it says so.
    LastExifRefd = CharBuf;

    FocalplaneXRes = 0;
    FocalplaneUnits = 0;
    ExifImageWidth = 0;

    if (ShowTags){
        printf("Exif header %d bytes long\n",length);
    }

    {   // Check the EXIF header component
        static const uchar ExifHeader[] = {0x45, 0x78, 0x69, 0x66, 0x00, 0x00};
        if (memcmp(CharBuf+2, ExifHeader,6)){
            ERREXIT("Incorrect Exif header");
        }
    }

    if (memcmp(CharBuf+8,"II",2) == 0){
        if (ShowTags) printf("Exif section in Intel order\n");
        MotorolaOrder = 0;
    }else{
        if (memcmp(CharBuf+8,"MM",2) == 0){
            if (ShowTags) printf("Exif section in Motorola order\n");
            MotorolaOrder = 1;
        }else{
            ERREXIT("Invalid Exif alignment marker.");
        }
    }

    // Check the next two values for correctness.
    if (Get16u(CharBuf+10) != 0x2a
      || Get32u(CharBuf+12) != 0x08){
        ERREXIT("Invalid Exif start (1)");
    }

    // First directory starts 16 bytes in.  Offsets start at 8 bytes in.
    ProcessExifDir(CharBuf+16, CharBuf+8, length-6);

    // Compute the CCD width, in milimeters.
    if (FocalplaneXRes != 0){
        ImageInfo.CCDWidth = (float)(ExifImageWidth * FocalplaneUnits / FocalplaneXRes);
    }

    if (ShowTags){
        printf("Thunbnail size of Exif header: %d\n",length-(LastExifRefd-CharBuf));
    }
}
 
//--------------------------------------------------------------------------
// Parse the marker stream until SOS or EOI is seen;
//--------------------------------------------------------------------------
static int scan_JPEG_header (FILE * infile,int ReadAll)
{
    int a;
    int HaveCom = FALSE;

    a = fgetc(infile);
    if (a != 0xff || fgetc(infile) != M_SOI){
        return FALSE;
    }

    for(SectionsRead=0;SectionsRead < 19;){
        int itemlen;
        int marker = 0;
        int ll,lh, got;
        uchar * Data;

        for (a=0;a<7;a++){
            marker = fgetc(infile);
            if (marker != 0xff) break;
        }
        if (marker == 0xff){
            // 0xff is legal padding, but if we get that many, something's wrong.
            ERREXIT("too many padding bytes!");
        }

        Sections[SectionsRead].Type = marker;
  
        // Read the length of the section.
        lh = fgetc(infile);
        ll = fgetc(infile);

        itemlen = (lh << 8) | ll;

        if (itemlen < 2){
            ERREXIT("invalid marker");
        }

        Sections[SectionsRead].Size = itemlen;

        Data = (uchar *)malloc(itemlen+1); // Add 1 to allow sticking a 0 at the end.
        if (Data == NULL){
            ERREXIT("Could not allocate memory");
        }
        Sections[SectionsRead].Data = Data;

        // Store first two pre-read bytes.
        Data[0] = (uchar)lh;
        Data[1] = (uchar)ll;

        got = fread(Data+2, 1, itemlen-2, infile); // Read the whole section.
        if (got != itemlen-2){
            ERREXIT("reading from file");
        }
        SectionsRead += 1;

        //printf("Marker '%x' size %d\n",marker, itemlen);
        switch(marker){
            case M_SOS:   // stop before hitting compressed data 
                // If reading entire image is requested, read the rest of the data.
                if (ReadAll){
                    int cp, ep, size;
                    // Determine how much file is left.
                    cp = ftell(infile);
                    fseek(infile, 0, SEEK_END);
                    ep = ftell(infile);
                    fseek(infile, cp, SEEK_SET);

                    size = ep-cp;
                    Data = (uchar *)malloc(size);
                    if (Data == NULL){
                        ERREXIT("could not allocate data for entire image");
                    }

                    got = fread(Data, 1, size, infile);
                    if (got != size){
                        ERREXIT("could not read the rest of the image");
                    }

                    Sections[SectionsRead].Data = Data;
                    Sections[SectionsRead].Size = size;
                    Sections[SectionsRead].Type = PSEUDO_IMAGE_MARKER;
                    SectionsRead ++;
                    HaveAll = 1;
                }
                return TRUE;

            case M_EOI:   // in case it's a tables-only JPEG stream
                printf("No image in jpeg!\n");
                return FALSE;

            case M_COM: // Comment section
                if (HaveCom){
                    SectionsRead -= 1;
                    free(Sections[SectionsRead].Data);
                }else{
                    process_COM(Data, itemlen);
                    HaveCom = TRUE;
                }
                break;

            case M_EXIF:
                if (SectionsRead <= 2){
                    // Seen files from some 'U-lead' software with Vivitar scanner
                    // that uses marker 31 later in the file (no clue what for!)
                    process_EXIF((char *)Data, itemlen);
                }
                break;


            case M_SOF0: 
            case M_SOF1: 
            case M_SOF2: 
            case M_SOF3: 
            case M_SOF5: 
            case M_SOF6: 
            case M_SOF7: 
            case M_SOF9: 
            case M_SOF10:
            case M_SOF11:
            case M_SOF13:
            case M_SOF14:
            case M_SOF15:
                process_SOFn(Data, marker);
                break;
            default:
                // skip any other marker siliently.
                break;
        }
    }
    return TRUE;
}

 
// Command line parsing code
static const char * progname;   /* program name for error messages */
 
//--------------------------------------------------------------------------
// Show the collected image info, displaying camera F-stop and shutter speed
// in a consistent and legible fashion.
//--------------------------------------------------------------------------
void ShowImageInfo(void)
{
    printf("File name    : %s\n",ImageInfo.FileName);
    printf("File size    : %d bytes\n",ImageInfo.FileSize);

    {
        char Temp[20];
        struct tm ts;
        ts = *localtime(&ImageInfo.FileDateTime);
        strftime(Temp, 20, "%Y:%m:%d %H:%M:%S", &ts);
        printf("File date    : %s\n",Temp);
    }

    if (ImageInfo.CameraMake[0]){
        printf("Camera make  : %s\n",ImageInfo.CameraMake);
        printf("Camera model : %s\n",ImageInfo.CameraModel);
    }
    if (ImageInfo.DateTime[0]){
        printf("Date/Time    : %s\n",ImageInfo.DateTime);
    }
    printf("Resolution   : %d x %d\n",ImageInfo.Width, ImageInfo.Height);
    if (ImageInfo.IsColor == 0){
        printf("Color/bw     : Black and white\n");
    }
    if (ImageInfo.FlashUsed >= 0){
        printf("Flash used   : %s\n",ImageInfo.FlashUsed ? "Yes" :"No");
    }
    if (ImageInfo.FocalLength){
        printf("Focal length : %4.1fmm",(double)ImageInfo.FocalLength);
        if (ImageInfo.CCDWidth){
            printf("  (35mm equivalent: %dmm)",
                        (int)(ImageInfo.FocalLength/ImageInfo.CCDWidth*35 + 0.5));
        }
        printf("\n");
    }

    if (ImageInfo.CCDWidth){
        printf("CCD Width    : %5.2fm\n",(double)ImageInfo.CCDWidth);
    }

    if (ImageInfo.ExposureTime){
        printf("Exposure time:%6.3f s ",(double)ImageInfo.ExposureTime);
        if (ImageInfo.ExposureTime <= 0.5){
            printf(" (1/%d)",(int)(0.5 + 1/ImageInfo.ExposureTime));
        }
        printf("\n");
    }
    if (ImageInfo.ApertureFNumber){
        printf("Aperture     : f/%4.1f\n",(double)ImageInfo.ApertureFNumber);
    }
    if (ImageInfo.Distance){
        if (ImageInfo.Distance < 0){
            printf("Focus Dist.  : Infinite\n");
        }else{
            printf("Focus Dist.  :%5.2fm\n",(double)ImageInfo.Distance);
        }
    }

    // Print the comment. Print 'Comment:' for each new line of comment.
    if (ImageInfo.Comments[0]){
        int a,c;
        printf("Comment      : ");
        for (a=0;a<200;a++){
            c = ImageInfo.Comments[a];
            if (c == '\0') break;
            if (c == '\n'){
                printf("\nComment      : ");
            }else{
                putchar(c);
            }
        }
        printf("\n");
    }

    printf("\n");
}

//--------------------------------------------------------------------------
// Discard read data.
//--------------------------------------------------------------------------
void DiscardData(void)
{
    int a;
    for (a=0;a<SectionsRead;a++){
        free(Sections[a].Data);
    }
    SectionsRead = 0;
    HaveAll = 0;
}

//--------------------------------------------------------------------------
// Read image data.
//--------------------------------------------------------------------------
int ReadJpegFile(char * FileName, int ReadAll)
{
    FILE * infile;
    int ret;
    int a;

    infile = fopen(FileName, "rb"); // Unix ignores 'b', windows needs it.

    if (infile == NULL) {
        fprintf(stderr, "%s: can't open '%s'\n", progname, FileName);
        return FALSE;
    }
    CurrentFile = FileName;

    // Start with an empty image information structure.
    memset(&ImageInfo, 0, sizeof(ImageInfo));
    memset(&Sections, 0, sizeof(Sections));


    // Extract pure path pice.
    for (a = strlen(FileName);a;){
        --a;
        #ifdef _WIN32
        if (FileName[a] == '\\') {
        #else
        if (FileName[a] == '/') {
        #endif
            a++;
            break; // Found first slash.
        }
    }
    strncpy(ImageInfo.FileName, FileName+a, 119);

    ImageInfo.FocalLength = 0;
    ImageInfo.ExposureTime = 0;
    ImageInfo.ApertureFNumber = 0;
    ImageInfo.Distance = 0;
    ImageInfo.CCDWidth = 0;
    ImageInfo.FlashUsed = -1;

    {
        // Store file date/time.
        struct stat st;
        if (stat(FileName, &st) >= 0){
            ImageInfo.FileDateTime = st.st_mtime;
            ImageInfo.FileSize = st.st_size;
        }else{
            ERREXIT("Can't get file statitics");
        }
    }

    // Scan the JPEG headers.
    ret = scan_JPEG_header(infile, ReadAll);
    if (!ret){
        printf("Invalid Jpeg file: '%s'\n",FileName);
    }

    fclose(infile);

    if (ret == FALSE){
        DiscardData();
    }
    return ret;
}

//--------------------------------------------------------------------------
// Remove exif thumbnail
//--------------------------------------------------------------------------
int RemoveThumbnail(void)
{
    int a;
    for (a=0;a<SectionsRead-1;a++){
        if (Sections[a].Type == M_EXIF){
            // Truncate the thumbnail section of the exif.
            unsigned int Newsize = LastExifRefd-(char *)Sections[a].Data;
            if (Sections[a].Size == Newsize) return FALSE; // Thumbnail already gonne.
            Sections[a].Size = Newsize;
            Sections[a].Data[0] = (uchar)(Newsize >> 8);
            Sections[a].Data[1] = (uchar)Newsize;
            return TRUE;
        }
    }
    // Not an exif image.  Don't know how to get rid of thumbnails.
    return FALSE;
}


//--------------------------------------------------------------------------
// Write image data back to disk.
//--------------------------------------------------------------------------
void WriteJpegFile(char * FileName)
{
    FILE * outfile;
    int a;

    if (!HaveAll){
        ERREXIT("Can't write back - didn't read all");
    }

    outfile = fopen(FileName,"wb");
    if (outfile == NULL){
        ERREXIT("Could not open file for write");
    }

    // Initial static jpeg marker.
    fputc(0xff,outfile);
    fputc(0xd8,outfile);
    
    // Write all the misc sections
    for (a=0;a<SectionsRead-1;a++){
        fputc(0xff,outfile);
        fputc(Sections[a].Type, outfile);
        fwrite(Sections[a].Data, Sections[a].Size, 1, outfile);
    }

    // Write the remaining image data.
    fwrite(Sections[a].Data, Sections[a].Size, 1, outfile);
       
    fclose(outfile);
}


//--------------------------------------------------------------------------
// Do selected operations to one file at a time.
//--------------------------------------------------------------------------
void ProcessFile(char * FileName)
{
    int MayModify = FALSE;
    int Modified = FALSE;

    FilesMatched = TRUE; // Turns off complaining that nothing matched.

    if (remove_thumbnails){
        MayModify = TRUE;
    }

    if (!ReadJpegFile(FileName, MayModify)) return;

    ShowImageInfo();

    if (remove_thumbnails){
        if (RemoveThumbnail()){
            Modified = TRUE;
        }
    }   

    if (Modified){
        char BackupName[400];
        strcpy(BackupName, FileName);
        strcat(BackupName, ".old");

        // Remove any .old file name that may pre-exist
        unlink(BackupName);

        // Rename the old file.
        rename(FileName, BackupName);

        // Write the new file.
        WriteJpegFile(FileName);

        // Now that we are done, remove original file.
        unlink(BackupName);
    }
    DiscardData();

}


//--------------------------------------------------------------------------
// complain about bad state of the command line.
//--------------------------------------------------------------------------
static void Usage (void)
{
    fprintf(stderr,"Program for extracting Digicam setting information from Exif Jpeg headers\n"
                   "used by most Digital Cameras.  v0.9  Matthias Wandel, April 2000.\n"
                   "http://www.sentex.net/~mwandel/jhead  mwandel@sentex.net\n"
                   "\n");

    fprintf(stderr,"Usage: %s [options] files\n", progname);
    fprintf(stderr,"Where:\n"
                   "[otpions] are:\n"
                   "   -t     -->      Remove exif thumbnails from exif files\n"
                   "   -r     -->      Recursive.\n"
                   "   -h     -->      help (this text)\n"
                   "   -v     -->      even more verbose output\n"
                   "files     -->      path/filenames with or without widlcards\n"
           );
                    

    exit(EXIT_FAILURE);
}

//--------------------------------------------------------------------------
// Checking of an extension.
//--------------------------------------------------------------------------
int ExtCheck(char * Ext, char * Pattern)
{
    for(;;Ext++,Pattern++){
        if (*Ext == *Pattern){
            if (*Ext == '\0') return 0; //Matches.
            continue;
        }
        if (*Ext > 'A' && *Ext <= 'Z'){
            if (*Ext+'a'-'A' == *Pattern) continue;
        }
        return 1; // Differs.
    }
}

//--------------------------------------------------------------------------
// Handle a pattern, possibly using subdirectories... (Linux)
//--------------------------------------------------------------------------
#ifndef _WIN32
static void HandleSubpath(char * Path)
{
    DIR * dirpt;

    char DirName[200];
    int a;
    struct stat filestat;

    if (stat(Path, &filestat)){
        printf("Error on '%s'\n",Path);
        return;
    }
    if(!S_ISDIR(filestat.st_mode)){
        // This is a file, not a directory.
        ProcessFile(Path);
        return;
    }

    strcpy(DirName,Path);
    a=strlen(DirName);

    if (DirName[a-1] != '/'){
        // Make sure it ends with '/'
        DirName[a] = '/';
        DirName[a+1] = '\0';
    }

    dirpt = opendir(DirName);
    if (dirpt == NULL){
         printf("Could not read directory: %s",strerror(errno));
         return;
    }

    for (;;){
        struct dirent * entry;
        static char FullPath[400];

        entry = readdir(dirpt);
        if (entry == NULL) break;

        strcpy(FullPath, DirName);
        strcat(FullPath, entry->d_name);

        if (stat(FullPath, &filestat)){
            printf("Error on '%s'\n",FullPath);
            continue;
        }

        if(!S_ISREG(filestat.st_mode)) continue; // Not a regular file.  Ignore.

        a=strlen(entry->d_name);

        if (a < 5) continue; // Too short a name to make sense (looking for .jpg extension)

        if (ExtCheck(entry->d_name+a-5, ".jpeg") != 0
            && ExtCheck(entry->d_name+a-4, ".jpg") != 0){
            // Its not .jpg or .jpeg.
            continue;
        }

        ProcessFile(FullPath);
    }
    closedir(dirpt);

    // Do subdirectories after doing the current directory.
    // This necessitates reading the directory twice.
    if (DoSubdirs){
        dirpt = opendir(DirName);
        for (;;){
            struct dirent * entry;
            static char FullPath[400];

            entry = readdir(dirpt);
            if (entry == NULL) break;

            strcpy(FullPath, DirName);
            strcat(FullPath, entry->d_name);

            if (stat(FullPath, &filestat)){
                printf("Error on '%s'\n",FullPath);
                continue;
            }

            if(!S_ISDIR(filestat.st_mode)) continue; // Not a directory.

            if (entry->d_name[0] == '.' || entry->d_name[0] == '_'){
                // Skip strange directory names (I use these for thumbnails)
            }else{
                HandleSubpath(FullPath);
            }
        }
        closedir(dirpt);
    }
}

#else
//--------------------------------------------------------------------------
// Handle a pattern, possibly using subdirectories... (Windows)
//--------------------------------------------------------------------------
static void HandleSubpath(char * Path)
{
    struct _finddata_t finddata;

    long find_handle;
    char ThisPattern[200];
    int a;

    strcpy(ThisPattern,Path);

    if (DoSubdirs){
        strcat(ThisPattern,"\\*");
    }
    find_handle = _findfirst(ThisPattern, &finddata);

    if (find_handle == -1){
        fprintf(stderr, "Error: No file matches '%s'\n",Path);
    }

    for (;;){
        static char CombinedPath[400];
        if (find_handle == -1) break;

        if (finddata.attrib & _A_SUBDIR) goto nextfile; // Its a subdirectory.

        a=strlen(finddata.name);

        if (a < 5) goto nextfile; // Too short a name to contain '.jpg'

        if (ExtCheck(finddata.name+a-5, ".jpeg") != 0
            && ExtCheck(finddata.name+a-4, ".jpg") != 0){
            // Its not .jpg or .jpeg.
            goto nextfile;
        }

        // This whole area is really gross, but below hack makes it so that
        // if I drag a single file on it on the desktop, it will find it
        // using the given paths.
        strcpy(CombinedPath, Path);
        a=strlen(Path);

        if (!DoSubdirs){
            for(;a;){
                a--;
                if (!a) {
                    CombinedPath[0] = 0;
                    break;
                }
                if (CombinedPath[a] == '\\'){
                    CombinedPath[a+1] = 0;
                    break;
                }
            }
        }else{
            strcat(CombinedPath, "\\");
        }
        strcat(CombinedPath, finddata.name);

        ProcessFile(CombinedPath);

        nextfile:

        if (_findnext(find_handle, &finddata) != 0) break;
    }

    _findclose(find_handle);

    if (DoSubdirs){
        find_handle = _findfirst(ThisPattern, &finddata);
        for (;;){
            if (find_handle == -1) break;

            if (finddata.attrib & _A_SUBDIR){
                // Its a subdirectory.
                if (finddata.name[0] == '.' || finddata.name[0] == '_'){
                    // Skip strange directory names (I use thes for thumbnails)
                }else{
                    strcpy(ThisPattern, Path);
                    strcat(ThisPattern, "\\");
                    strcat(ThisPattern, finddata.name);
                    HandleSubpath(ThisPattern);
                }
            }
            if (_findnext(find_handle, &finddata) != 0) break;
        }

        _findclose(find_handle);
    }
}

#endif

//--------------------------------------------------------------------------
// The main program.
//--------------------------------------------------------------------------
int main (int argc, char **argv)
{
    int argn;
    char * arg;
    progname = argv[0];

    for (argn=1;argn<argc;argn++){
        arg = argv[argn];
        if (arg[0] != '-') break; // Filenames from here on.
        if (!strcmp(arg,"-v")){
            ShowTags = TRUE;
        }else if (!strcmp(arg,"-t")){
            remove_thumbnails = TRUE;
        }else if (!strcmp(arg,"-r")){
            DoSubdirs = TRUE;
        }else if (!strcmp(arg,"-h")){
            Usage();
        }else{
            printf("Argument '%s' not understood\n",arg);
            Usage();
        }
    }
    if (argn == argc){
        fprintf(stderr,"Error: Must supply a file name\n");
        Usage();
    }

    for (;argn<argc;argn++){
        FilesMatched = FALSE;
        
        HandleSubpath(argv[argn]);

        if (!FilesMatched){
            fprintf(stderr, "Error: No files matched '%s'\n",argv[argn]);
        }
    }
    return EXIT_SUCCESS;
}

