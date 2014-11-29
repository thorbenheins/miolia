/**
 * Created by thorben on 29/11/14.
 */

(function ($) {
    //Here jQuery is $
    $(document).ready(function(){
        $('#product_url').on('change', function(){
            $("#product_url").after("<div id='spinner_product_url' class='spinner'><img src='/images/spinner.gif'/></div>")
            $.post("/app_dev.php/api/v1/productInfo", {url: this.value}, function(data){

                $("#product_name").val(data.name);
                $("#product_description").val(data.description);
                $("#product_price").val(data.price);
                $("#product_image").val(data.image);
                $("#product_imageThumbnail").val(data.imageThumbnail);

                if ()

                $("#product_image").after('<img height="200px" src="'+data.image+'">');
                $("#product_imageThumbnail").after('<img src="'+data.imageThumbnail+'">');

                //console.debug(data)
            }).always(function() {
                $("#spinner_product_url").remove();
            });;
        });
    });
})(jQuery);