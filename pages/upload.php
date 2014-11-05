<?php

function body(){
          global $base;
?>

            <script src="/<?php echo $base; ?>js/uploader.js"></script>            
            <div id="fileGroup"class="form-group"> 
              <div style='min-height:220px;'>
              <label for="videoName">Name</label>
                <input id="videoName" name="videoName" type="text" placeholder="Name" class="form-control input-md"><br />
              <label for="description">Description</label>
                <textarea class="form-control input-md" placeholder="Video description" id='description' rows=8></textarea><br />
              <label for="description">Visibility</label>
              <div class="radio">
                <label>
                  <input type="radio" name="optionsRadios" id="public" value="option1" checked>
                  Public
                </label>
              </div>
              <div class="radio">
                <label>
                  <input type="radio" name="optionsRadios" id="private" value="option2">
                  Private
                </label>
              </div><br />
              <label for="file">Video</label><br />
                <span>Please select a video to upload</span><span class='fileUpload'><input type="file" class='upload' name="file" id="file" onchange="new uploader(this.files[0])" /></span><br />
                <div style='width: 100%; height: 40px; position: relative;'> 
                  <div style='border-radius: 3px; -webkit-animation: super-rainbow 15s infinite linear; -moz-animation: super-rainbow 15s infinite linear; position: absolute; top: 0px; left: 0px; height: 40px; width: 100%;'></div> 
                  <div id='uploadBar' style='position: absolute; top: 0px; right: 0px; height: 40px; width: 100%; background-color: #aaa; opacity: 0.5;'></div> 
                  <div id='uploadProgress' style='position: absolute; top: 0px; right: 0px; width: 100%; text-align: center; vertical-align: middle; font-size: 27px; color: #fff; display:table-cell; font-weight: bold;'>0%</div> 
                </div> 
              </div>
            </div> 
<?php


}

?>