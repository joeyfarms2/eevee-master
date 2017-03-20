<style type="text/css">
<!--
.extracted_thumb {
  float: left;
  margin-right: 10px;
}

#loading_indicator{
  position: absolute;
  margin-left: 480px;
  margin-top: 8px;
  display:none;
}
#extracted_results{
  display:none;
}
.thumb_sel {
  float: left;
  height: 22px;
  width: 55px;
}
.thumb_sel .prev_thumb {
  background: url(/images/thumb_selection.gif) no-repeat -50px 0px;
  float: left;
  width: 26px;
  height: 22px;
  cursor: hand;
  cursor: pointer;
}
.thumb_sel .prev_thumb:hover {
  background: url(/images/thumb_selection.gif) no-repeat 0px 0px;
}
.thumb_sel .next_thumb {
  background: url(/images/thumb_selection.gif) no-repeat -76px 0px;
  float: left;
  width: 24px;
  height: 22px;
  cursor: hand; 
  cursor: pointer;
}
.thumb_sel .next_thumb:hover {
  background: url(/images/thumb_selection.gif) no-repeat -26px 0px;
}
.small_text{
  font-size: 10px;
}
-->
</style>
<script type="text/javascript" src="<?=SCRIPT_PATH?>additional/tinymce/tinymce.min.js"></script>

<!-- Modal -->
<div class="modal fade" id="modal_front_post_news" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Post news</h4>
      </div>
      <div class="modal-body" style="height:400px; overflow-y:auto;">
        
        <div class="result-msg"></div>

        <form id="frm_news" name="frm_news" method="POST" action="<?=site_url('news/post')?>" class="" enctype="multipart/form-data">
          <input type="hidden" id="news_aid" name="news_aid" value="" />
          <input type="hidden" id="command" name="command" value="" />
          <input type="hidden" id="status" name="status" value="" />
          
          <input type="hidden" id="ref_link2_url" name="ref_link2_url" value="" />
          <input type="hidden" id="ref_link2_image_url" name="ref_link2_image_url" value="" />
          <input type="hidden" id="ref_link2_title" name="ref_link2_title" value="" />
          <input type="hidden" id="ref_link2_desc" name="ref_link2_desc" value="" />

      
          <div class="form-group">
            <label>Title</label>
            <input class="form-control" type="text" id="title" name="title" value="" maxlength="100" />
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea class="form-control" id="description_1" name="description_1"></textarea>
            <textarea class="hidden" id="description" name="description"></textarea>
          </div>

          <div class="form-group">
            <label>Image(s)</label>
            <input class="default" type="file" id="image_name" name="image_name[]" multiple="" accept="image/*" />
            <small><em class="help-block" id="image_name_limit_for_default">* Only file extension <?=get_file_type(CONST_ALLOW_FILE_TYPE_FOR_IMAGE)?> and size not over <?=get_size(CONST_ALLOW_FILE_SIZE_FOR_IMAGE)?>.</em></small>

            <div id="panel_news_gallery_list"></div>
          </div>

          <div class="form-group extract_url">
              <label>Reference URL</label>
              <img id="loading_indicator" src="images/ajax-loader.gif">
              <textarea id="get_url" placeholder="Enter your URL here" class="get_url_input form-control" spellcheck="false"></textarea>
              <div id="extracted_results" class="ptm pbm">
              </div>
          </div>

      
         <!--  <div class="form-group">
            <label>Reference URL</label>
            <input class="form-control" type="text" id="ref_link" name="ref_link" value="" placeholder="http://">
          </div> -->

          <div class="form-group">
            <label>Category</label>
            <fieldset>
              <?php foreach ($news_cat_result as $key => $item) { ?>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" id="category_<?=$item['aid']?>" name="category[]" value="<?=$item['aid']?>"> <?=$item['name']?>
                  </label>
                </div>
              <?php } ?>
            </fieldset>
          </div>


          
        </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button id="btn_post_save_news" type="button" class="btn btn-primary">Post</button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  $(document).ready(function($){
    tinymce.init({
        selector: "#description_1",
        height: 150,
        theme: "modern",
        plugins: [
        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen",
        "insertdatetime media nonbreaking save table contextmenu directionality",
        "emoticons template paste textcolor colorpicker textpattern moxiemanager"
      ],
        toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | forecolor backcolor emoticons",
        image_advtab: true,
    });

    $('#btn_post_save_news').click(function() {
      $('#description').val( tinyMCE.get('description_1').getContent() );
      if ($.trim($('#get_url').val()) !== "") {
          $('#ref_link2_url').val( $.trim($('#get_url').val()) );
          $('#ref_link2_image_url').val( $('#extracted_thumb').find('img').attr('src') );
          $('#ref_link2_title').val( $('.extracted_content').find('h4').text() );
          $('#ref_link2_desc').val( $('.extracted_content').find('p').text() );
      }
      $('#frm_news').addClass('hidden');
      $('#modal_front_post_news').find('.result-msg').html('<i class="fa fa-spinner fa-spin"></i> Posting...').removeClass('hidden');
      $('#modal_front_post_news').find('.modal-body').css('height', 'auto');
      $('#frm_news').submit();
    });





    var getUrl  = $('#get_url'); //url to extract from text field
    
    getUrl.on('keyup', function( event, img_src ) { //user types url in text field

        if ($.trim(getUrl.val()) == "") {
          $('#ref_link2_url').val("");
          $('#ref_link2_image_url').val("");
          $('#ref_link2_title').val("");
          $('#ref_link2_desc').val("");
          $("#extracted_results").html("");
          return false;
        }
        
        //url to match in the text field
        var match_url = /\b(https?):\/\/([\-A-Z0-9.]+)(\/[\-A-Z0-9+&@#\/%=~_|!:,.;]*)?(\?[A-Z0-9+&@#\/%=~_|!:,.;]*)?/i;
        
        //continue if matched url is found in text field
        if (match_url.test(getUrl.val())) {
                // $("#extracted_results").hide();
                $("#loading_indicator").show(); //show loading indicator image
                
                var extracted_url = getUrl.val().match(match_url)[0]; //extracted first url from text filed
                
                $("#extracted_results").html("<i class='fa fa-spinner fa-spin'></i> Loading...");

                //ajax request to be sent to extract-process.php
                var post_url = "<?=site_url('news/ajax-extract-url-process/'.rand())?>";
                $.post(post_url, {'url': extracted_url}, function(data){       
                    extracted_images = data.images;
                    total_images = parseInt(data.images.length-1);
                    // img_arr_pos = total_images;
                    img_arr_pos = 1;
                    
                    if(total_images>0){
                        inc_image = '<div class="extracted_thumb" id="extracted_thumb"><img src="'+data.images[img_arr_pos]+'" width="120" height="120" class="img-preview"></div>';
                    }else{
                        inc_image ='';
                    }
                    //content to be loaded in #extracted_results element
                    var content = '<div class="extracted_url">'+ inc_image +'<div class="extracted_content"><h4><a href="'+extracted_url+'" target="_blank">'+data.title+'</a></h4><p>'+data.content+'</p><div class="thumb_sel"><span class="prev_thumb" id="thumb_prev">&nbsp;</span><span class="next_thumb" id="thumb_next">&nbsp;</span> </div><span class="small_text" id="total_imgs">'+img_arr_pos+' of '+total_images+'</span><span class="small_text">&nbsp;&nbsp;Choose a Thumbnail</span></div></div>';
                    
                    //load results in the element
                    $("#extracted_results").html(content); //append received data into the element
                    $("#extracted_results").slideDown(); //show results with slide down effect
                    $("#loading_indicator").hide(); //hide loading indicator image

                    if ($.trim(img_src) != '' && $.trim(img_src) != 'null' && $.trim(img_src) != 'undefined') {
                        $('#extracted_results').find('img.img-preview').attr('src', img_src);
                    }
                },'json');
        }

    });





    //user clicks previous thumbail
    $("body").on("click", "#thumb_prev", function(e){        
        if(img_arr_pos>0) 
        {
            img_arr_pos--; //thmubnail array position decrement
            
            //replace with new thumbnail
            $("#extracted_thumb").html('<img src="'+extracted_images[img_arr_pos]+'" width="120" height="120">');
            
            //replace thmubnail position text
            $("#total_imgs").html((img_arr_pos) +' of '+ total_images);
        }
    });
    
    //user clicks next thumbail
    $("body").on("click","#thumb_next", function(e){        
        if(img_arr_pos<total_images)
        {
            img_arr_pos++; //thmubnail array position increment
            
            //replace with new thumbnail
            $("#extracted_thumb").html('<img src="'+extracted_images[img_arr_pos]+'" width="120" height="120">');
            
            //replace thmubnail position text
            $("#total_imgs").html((img_arr_pos) +' of '+ total_images);
        }
    });



  });
</script>