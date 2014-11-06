<?php

error_reporting(E_ALL);

global $entity;

if(!$entity->isAdmin()){
    exit;
}


function body(){

	?>
<style type='text/css'>
#status td, #status th {
	padding-top: 5px;
	padding-bottom: 5px;
	padding-left: 10px;
	padding-right: 10px;
}

#status tbody tr:nth-child(even) {
    background-color: #fff;
}

#status tbody tr:nth-child(odd) {
    background-color: #efefef;
}

#status tbody .busy {
	color: #bbb;
}

#status tbody .busy .btn-danger {
	background-color: #aaa;
	background-repeat: no-repeat;
	background-image: none;
}

</style>
<script>

var send = {
    /**
     * Generate a new AJAX option, cross browser
     */
    genxmlhttp: function() {
        if (window.XMLHttpRequest) {
            return new XMLHttpRequest();
        } else {
            return new ActiveXObject("Microsoft.XMLHTTP");
        }
    },
    /**
     * Do the actual AJAX (POST) request and handle the returned values
     * 
     * @param String data The (POST) data to send to the server
     * @param String location The file on the server to send the data to
     * @param function callback Called when the sending is successfull
     * @param function failCallback Called when the send attempt fails, will also be called on server error
     */
    sendData: function(data, location, callback, failCallback) {
        var xmlhttp = this.genxmlhttp();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status >= 400 && failCallback) {
                failCallback(xmlhttp.responseText);
            }
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200 && callback) {
                callback(xmlhttp.responseText);
            }
        };

        xmlhttp.open("POST", location, true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send(data);
    }
}

function setBusy(el){
	el.setAttribute("class",el.getAttribute("class")+" busy");
}

function unsetBusy(el){
	el.setAttribute("class", el.getAttribute("class").replace("busy",""));
}

function confirmDelete(id, name, el){
	if(busyIds[id]){
		return;
	}
	if(window.confirm("Do you really want to delete: "+name) && window.confirm("Are you sure?")){
		busyIds[id] = true;
		send.sendData("action=delete&id="+id,'/serverBackend/status.php', function(response){
			el.parentNode.parentNode.removeChild(el.parentNode);
			// window.alert(name + " deleted");
		}, 
		function(){
			window.alert("Something went wrong");
		});
		setBusy(el.parentNode);
	}
}

function newServer(){
	if(!window.confirm("Are you sure you want to create a new server?")){
		return;
	}
	var button = "<button type=\"button\" class=\"btn btn-danger\" onclick=\"confirmDelete('','',this.parentNode)\"><span class=\"glyphicon glyphicon-remove-sign\"></span></button>";
	var row = document.createElement("tr");
	row.setAttribute("class", "busy");
	row.innerHTML = "<td>New server</td><td></td><td></td><td></td><td></td><td></td><td>" + button + "</td>";

	send.sendData("action=new&size=1",'/serverBackend/status.php', function(response){
		unsetBusy(row);
		row.innerHTML = "<td>Server ready</td><td></td><td></td><td></td><td></td><td></td><td>" + button + "</td>";
	}, 
	function(){
		window.alert("Something went wrong");
	});

	document.getElementById('statusTable').appendChild(row);
	newLines[newLines.length] = row;

}

var busyIds = [];
var newLines = [];

send.sendData('','/serverBackend/status.php', parseResponse);
window.setInterval(function(){
	send.sendData('','/serverBackend/status.php', parseResponse);
}, 5000);


function parseResponse(response){
	console.log(response);
	var json = JSON.parse(response);

	var html = "<table id='statusTable'><thead><tr><th>Name</th><th>IP</th><th>Memory (MB)</th><th># CPUs</th><th>Load</th><th>Job status</th><th>Actions</th></tr></thead><tbody>";
	for(var i in json){
		var ob = json[i];
		var freeMem = ob.freeMemory;
		// window.alert(freeMem);
		if(freeMem){
			if(freeMem.indexOf("\n")>-1){
				freeMem = freeMem.split("\n")[0];
			}
			var percentage = Math.round((freeMem) / (ob.memory) * 100);
			var clss = "";
			if(busyIds[ob.id]){
				clss="class='busy'";
			}
			html += "<tr " + clss + "><td>" +
						ob.name + "</td><td>" +
						ob.ip + "</td><td>" +
						ob.memory + " (Free: " + freeMem + ", "+percentage+"%)" + "</td><td>" +
						ob.vcpus+ "</td><td>" +
						ob.tenMinLoad+" "+ ob.fiveMinLoad.replace('average:','0.00')+" "+ob.oneMinLoad.replace('load','0.00') + "</td><td>" +
						ob.jobStatus.replace("\n","") + "</td><td>" +
						
						"<button type=\"button\" class=\"btn btn-danger\" onclick=\"confirmDelete('" + ob.ip + "','" + ob.name + "',this.parentNode)\"><span class=\"glyphicon glyphicon-remove-sign\"></span></button>" + "</td><td>" +
					"</td></tr>";
		} else {
			if(ob.memory == "None")
				html += "<tr><td><b><u>Creating " + ob.name + "</u></b></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
			else
				html += "<tr><td><b><u>" + ob.name + " offline!</u></b></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
		}
	}
	html += "</tbody></table><br /><button type=\"button\" class=\"btn btn-success\" onclick=\"newServer()\">New server <span class=\"glyphicon glyphicon-plus-sign\"></span></button>";
	var status = document.getElementById('status');
	status.innerHTML = html;
	for(var i = 0; i < newLines.length; i++)
		status.lastChild.lastChild.appendChild(newLines[i]);
}

</script>
<div id='status'>
	<center><h1>Loading status page, please wait</h1></center>
</div>
<?php
}
?>
