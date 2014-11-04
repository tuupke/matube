function uploader(toUploadFile){
    var BYTES_PER_CHUNK = 1024*1024; // 1MB chunk size
    var chunk_size = BYTES_PER_CHUNK;
    var slices = 0;
    var slice_method;
    var file = toUploadFile;
    var range_start = 0;
    var range_end = BYTES_PER_CHUNK;
    var url = "http://matube.gehack.nl/upl.php";
    var doneF = "http://matube.gehack.nl/upl_done.php";
    var xmlhttp = new XMLHttpRequest();
    var name = false;

    var fileName = toUploadFile.name.split(".");
    var extension = fileName.pop();
    fileName = fileName.join(".");
    var file_size = toUploadFile.size;
    var done = false;
    var totalSlices = Math.ceil(file_size / BYTES_PER_CHUNK);

    if ('mozSlice' in file) {
        slice_method = 'mozSlice';
    }
    else if ('webkitSlice' in file) {
        slice_method = 'webkitSlice';
    }
    else {
        slice_method = 'slice';
    }

    function uploadFile(){
        var chunk;
            var add = "?ext="+extension;
            if(name){
                add += "&file="+name;
            }
            // Setup AJAX request
            xmlhttp.open('PUT', url+add, false);
            xmlhttp.overrideMimeType('application/octet-stream');
            if (range_end > file_size) {
                range_end = file_size;
            }
            xmlhttp.setRequestHeader('Content-Range', 'bytes ' + range_start + '-' + range_end + '/' + file_size);


            // Prepare chunk

            chunk = file[slice_method](range_start, range_end);
            xmlhttp.onload = chunkUploaded;
            xmlhttp.send(chunk);
    }

    function chunkUploaded(e){
        name = e.target.responseText;

        slices++;
        if (range_end === file_size) {
            fileUploaded();
            return;
        }
        var per = slices / totalSlices * 100;
        document.getElementById('uploadProgress').innerHTML = Math.floor(per) + '%';
        document.getElementById('uploadBar').style.width = (100-per) + '%';
         
        // Update our ranges
        range_start = range_end;
        range_end = range_start + chunk_size;
         
        // Next chunk
        
        setTimeout(function(){uploadFile()},1);

    }

    function fileUploaded(){
        if (done){
            return;
        }
        done = true;
        xmlhttp.open('GET', doneF+"?video="+name+"&ext="+extension+"&name="+encodeURIComponent(fileName), true);
        xmlhttp.send("");
        // xmlhttp.setRequestHeader('Content-Range', 'bytes ' + range_start + '-' + range_end + '/' + file_size);

        document.getElementById('uploadProgress').innerHTML = '100%';
        document.getElementById('uploadBar').style.width = '0%';
        document.getElementById('uploadProgress').innerHTML = "Upload completed";
    }

    uploadFile();
}
