<button id="ebz-checkout-btn">Proceed to Pay 50</button>
<script src="https://ebz-static.s3.ap-south-1.amazonaws.com/easecheckout/v2.0.0/easebuzz-checkout-v2.min.js"></script> 
<script>
   var easebuzzCheckout = new EasebuzzCheckout('3UDGABY1AP', 'prod'); // for test enviroment pass "test"
   document.getElementById('ebz-checkout-btn').onclick = function(e){
       var options = {
           access_key: $data, // access key received via Initiate Payment
           onResponse: (response) => {
               console.log(response);
           },
           theme: "#123456" // color hex
       }
       easebuzzCheckout.initiatePayment(options);
   }
</script>