#
# Makefile for MiG utils
#
#     MiG - http://tangledhelix.com/software/mig/
#     Dan Lowe <dan@tangledhelix.com>
#
# You shouldn't need to change anything below, unless you don't have gcc
# available, in which case you'll need to modify "CC" to point to your
# system's compiler.
#
# This has been tested with Solaris and Linux, but should work on most
# Unix or Unix-like platforms.  I have no idea what it will do on a Windows
# platform and I have no Windows-based compilers to test it with.
#
# For information about jhead, see the file docs/Utilities.txt.
#

CC = gcc
LIBS = -lm

default:
	@echo "To build jhead, type \"make jhead\"."
	@echo "To start fresh, type \"make jhead-clean ; make jhead\"."

jhead:
	${CC} ${LIBS} -o jhead jhead.c

jhead-clean:
	/bin/rm -f jhead
