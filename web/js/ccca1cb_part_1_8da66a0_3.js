/**
 * Created by thorben on 29/11/14.
 */

(function ($) {
    //Here jQuery is $
    $(document).ready(function(){
        $('#product_url').on('change', function(){
            $("#product_url").after("<div id='spinner_product_url' class='spinner'><img src='/images/spinner.gif'/></div>")
            $.post(AMILIO.config.base_url+"/api/v1/productInfo", {url: this.value}, function(data){

                //TODO do this a bit nicer. 

                $("#product_name").val(data.name);
                $("#product_description").val(data.description);
                $("#product_price").val(data.price);
                $("#product_image").val(data.image);
                $("#product_imageThumbnail").val(data.imageThumbnail);

                $("#product_image").after('<img height="200px" src="'+data.image+'">');
                $("#product_imageThumbnail").after('<img src="'+data.imageThumbnail+'">');

                $(".step_2").show();
		$("#step_1").hide();                
                //console.debug(data)
            }).always(function() {
                $("#spinner_product_url").remove();
            });;
        });
    });
})(jQuery);
