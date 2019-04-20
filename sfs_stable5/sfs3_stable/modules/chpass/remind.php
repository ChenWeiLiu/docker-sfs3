<?php
header('Content-Type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>


<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- development version, includes helpful console warnings -->
<!-- <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>  -->
<script src="resources/vue.js"></script>
<script src="resources/axios.min.js"></script>
<title>Title of the document</title>

<style type="text/css">
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, .5);
  display: table;
  transition: opacity .3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}

.modal-container {
  width: 400px;
  margin: 0px auto;
  padding: 20px 30px;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
  transition: all .3s ease;
  font-family: Helvetica, Arial, sans-serif;
}

.modal-header h3 {
  margin-top: 0;
  color: #42b983;
}

.modal-body {
  margin: 20px 0;
}

.modal-default-button {
  float: right;
}

/*
 * The following styles are auto-applied to elements with
 * transition="modal" when their visibility is toggled
 * by Vue.js.
 *
 * You can easily play with the modal transition by editing
 * these styles.
 */

.modal-enter {
  opacity: 0;
}

.modal-leave-active {
  opacity: 0;
}

.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}
</style>
</head>

<body>
<?php
if(isset($_SERVER['HTTP_USER_AGENT'])){
	$browser=$_SERVER['HTTP_USER_AGENT'];
	if(strpos($browser,'MSIE')!==FALSE || strpos($browser,'Trident')!==FALSE){
		header("Location: teach_cpass.php?alert=ok");
	}
}
?>

<!-- template for the modal component -->
<script type="text/x-template" id="modal-template">
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-container">

          <div class="modal-header">
            <slot name="header">
              default header
            </slot>
          </div>

          <div class="modal-body">
            <slot name="body">
              default body
            </slot>
          </div>

          <div class="modal-footer">
            <slot name="footer">
              default footer
              <button class="modal-default-button" @click="$emit('close')">
                OK
              </button>
            </slot>
          </div>
        </div>
      </div>
    </div>
  </transition>
</script>



<div id="app">

  <!-- use the modal component, pass in the prop -->
  <modal v-if="showModal" @close="console.log('sayyy')">
    <!--
      you can use custom content here to overwrite
      default content
    -->
    <h3 slot="header"> 您好</h3>
    <p slot="body">您已超過{{duration}}天未修改密碼，為了保障資料安全，提醒您定期更改密碼喔！
        <br />

	<a href="resources/information.pdf" class="btn btn-info" target="_blank" role="button">資安實施原則</a>
    </p>
    <p slot="footer">
	 <button class="modal-default-button btn btn-default" disabled>
	     {{display_seconds}} 秒後關閉
         </button>
	 <button class="modal-default-button btn btn-success" @click="main">
               瞭解
         </button>
	 <button class="modal-default-button btn btn-primary" @click="change">
              立即前往 
         </button>
    </p>
  </modal>
  {{countdown}}
</div>

<footer>
<script>



       Vue.prototype.$http = axios;
        let config = {
            headers: {
                'content-type': 'application/json;CHARSET=UTF-8'
            }
        };

	// register modal component
	Vue.component('modal', {
	  props: [],
	  template: '#modal-template',
           data: function() {
            return {
            }
           },
	   methods: {
           }
	})

	var app = new Vue({
	  //delimiters: ['<%', '%>'],
	  el: '#app',
	  data: {
	    duration: 90,
	    showModal: false,
	    display_seconds: 5,
	    disable_this: 0,
	    main_page: ''
	  },
	  methods: {
		main: function(){
			//console.log(this.main_page);
			this.showModal=false;
			let expression = /^https?/ ;	  
			let regex = new RegExp(expression);          
			if(this.main_page.match(regex)) {
				window.location.replace(this.main_page);
			}
		},
		change: function(){
			this.showModal=false;
			window.location.replace("teach_cpass.php");
		},
	  },
	  created: function(){
		
		let url = "remind.rest.php";
		let vm = this;
		//get value
                this.$http.get(url, config).then((response) => {
			console.log(response.data);
			vm.disable_this = response.data.disable_this;
			vm.display_seconds = response.data.display_seconds;
			vm.duration = response.data.duration;
			vm.main_page = response.data.main_page;
			
			
			if(vm.disable_this === "1"){
				console.log("redirec to main page");
				window.location.replace(this.main_page);	
			} else {
				vm.showModal = true;
			}


                }, (response) => {
                    // error callback
			console.log("error");
                });

		//避免相對路徑
	        let expression = /^https?/ ;	  
		if(!this.main_page.match(this.main_page)){
		  window.location.replace("https://www.tc.edu.tw");
		}
	  },
	  computed: {
		countdown: function() {
		
		    if(this.showModal === true ) {
			 let count = setInterval(() => {

				this.display_seconds -= 1;
				//console.log(this.display_seconds);
				if(this.display_seconds <=0) {
					this.showModal = false;
					this.main();
					clearInterval(count);
				}

			},1000);	
		    }	
		}
	  },
	})
</script>
</footer>
</body>

</html>
