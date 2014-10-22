<?php


function body(){

?>
            <div id="fileGroup"class="form-group"> 
              <label for="file">Video</label> 
              <div class="form-control" style='min-height:120px;'>
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