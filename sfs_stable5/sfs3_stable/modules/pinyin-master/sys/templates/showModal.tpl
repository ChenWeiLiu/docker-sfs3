<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<script src="js/vue.js"> </script>
	
</head>
<body>
<header>
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
                width: 300px;
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
                border: 4px solid #26759E;
                -webkit-box-shadow: #878787 0px 2px 2px;
                -moz-box-shadow: #878787 0px 2px 2px;
                box-shadow: #878787 0px 2px 2px;
                -webkit-border-radius: 23px;
                -moz-border-radius: 23px;
                border-radius: 23px;
                font-size: 13px;
                font-family: arial, helvetica, sans-serif;
                padding: 10px 20px 10px 20px;
                text-decoration: none;
                display: inline-block;
                text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.3);
                font-weight: bold;
                color: #FFFFFF;
                background-color: #3093C7;
                background-image: -webkit-gradient(linear, left top, left bottom, from(#3093C7), to(#1C5A85));
                background-image: -webkit-linear-gradient(top, #3093C7, #1C5A85);
                background-image: -moz-linear-gradient(top, #3093C7, #1C5A85);
                background-image: -ms-linear-gradient(top, #3093C7, #1C5A85);
                background-image: -o-linear-gradient(top, #3093C7, #1C5A85);
                background-image: linear-gradient(to bottom, #3093C7, #1C5A85);
                filter: progid: DXImageTransform.Microsoft.gradient(GradientType=0, startColorstr=#3093C7, endColorstr=#1C5A85);
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
        </header>

<!-- app -->
<div id="app">
  <!-- use the modal component, pass in the prop -->
  <modal v-if="showModal" @close="showModal = false">
	  <h3 slot="header"> 異動結果 </h3>
	  <p slot="body" :infoMsg="infoMsg"> {{infoMsg}}</p>
  </modal>
</div>




<footer>
	<!-- template for the modal component -->
<script type="text/x-template" id="modal-template">
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-container">

          <div class="modal-header">
            <slot name="header">
            </slot>
          </div>

          <div class="modal-body">
            <slot name="body">
               更新資料庫成功,請關閉本視窗重新開啟
            </slot>
          </div>

          <div class="modal-footer">
            <slot name="footer">
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


	<script>
	// register modal component
	Vue.component('modal', {
	  template: '#modal-template'
	})

	// start app
	new Vue({
	  el: '#app',
	  data: {
	    showModal: <{$showModal}>,
	    infoMsg: '<{$infoMsg}>'
	  }
	})
	</script>
</footer>



	
</body>
</html>

