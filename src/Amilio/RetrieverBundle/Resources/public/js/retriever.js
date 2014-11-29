/**
 * Created by thorben on 29/11/14.
 */

//Do the ajax request for the url

(function ($) {
    //Here jQuery is $
    //var Book = $(document.body).text();
    $(document).ready(function(){
        $('#product_url').on('change', function(){
            $.post("/app_dev.php/api/v1/productInfo", {url: this.value}, function(data){
                $("#product_name").val(data.name);
                $("#product_description").val(data.description);
                $("#product_price").val(data.price);
                //$("product_name").text(data.name);

                console.debug(data)
            });
        });
    });

  //  var _myProduct = null;

})(jQuery);
//Amilio.getUrl('product_info')
//()();