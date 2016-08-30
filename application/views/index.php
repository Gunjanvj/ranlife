<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>
function openMethod(evt, methodName) {
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the link that opened the tab
    document.getElementById(methodName).style.display = "block";
    evt.currentTarget.className += " active";
}
</script>

<title>PDF file</title>
<style>
input[type=number], select { 
     width: 60%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

input[type=submit] {  
   
    background-color: #4CAF50;
    color: white;
    padding: 14px 20px;
  
    border: none;
    border-radius: 4px;
    cursor: pointer;
}


input[type=submit]:hover {
    background-color: #45a049;
}

div {
    border-radius: 5px;
    background-color: #f2f2f2;
    padding: 20px;
}
label{
     border-radius: 5px;
    background-color: #f2f2f2;
    padding: 15px;
}
/* Style the list */
ul.tab {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
}

/* Float the list items side by side */
ul.tab li {float: left;}

/* Style the links inside the list items */
ul.tab li a {
    display: inline-block;
    color: black;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
    transition: 0.3s;
    font-size: 17px;
}

/* Change background color of links on hover */
ul.tab li a:hover {background-color: #ddd;}

/* Create an active/current tablink class */
ul.tab li a:focus, .active {background-color: #ccc;}

/* Style the tab content */
.tabcontent {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
}
</style>
</head>
<body>
<ul class="tab">
  <li><a href="#" class="tablinks" onclick="openMethod(event, 'Splitter')">Splitter</a></li>
  <li><a href="#" class="tablinks" onclick="openMethod(event, 'Merger')">Merger</a></li>
  <li><a href="#" class="tablinks" onclick="openMethod(event, 'Compressor')">Compressor</a></li>
  <li><a href="#" class="tablinks" onclick="openMethod(event, 'Signature')">Signature</a></li>
</ul>

<div id="Splitter" class="tabcontent">
<h3>Upload PDF file to Split</h3>
<div id="error_message" style="color:red"></div>
<div>
    <form method="post" id='pdf_splitter' name='pdf_splitter' enctype="multipart/form-data">  
    <div>
    <label>Upload PDF  </label>
    <input type="file" name="pdf_file" id="pdf_file" required>
    </div>
    <div>            
    <label>Split After  </label>
    <input type="number" name="pages" id="pages" >   
    </div>
    <input type="submit" name="btn-upload" id="upload_pdf" value="Upload">
    </form>
</div>
</div>

</body>
</html>
<script>//binds to onchange event of your input field
$('#pdf_file').bind('change', function() {
  //this.files[0].size gets the size of your file.
  	if((this.files[0].size) > 2.5e+7){
	$("#upload_pdf").prop('disabled', true);
	$( "#error_message" ).text('File size is exceeding the maximum size limit 25MB.');     
 	}
 	else{
		$("#error_message").text('');     
	 	$("#upload_pdf").prop('disabled', false);
		 
   	 }
});
$("form#pdf_splitter").submit(function(){
 $('#pdf_splitter').prop('action', "<?php echo site_url('request/split/');?>");
});
</script>
