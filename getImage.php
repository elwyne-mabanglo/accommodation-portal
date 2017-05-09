<?php
# GetImage - retrieves image from the database and outputs it.
# 
# Reference Material:
# - getImage: http://stuweb.cms.gre.ac.uk/~ha07/web/PHP/imageUpload.html
#
# Setup connection
$link = mysqli_connect("mysql.cms.gre.ac.uk", "me324", "12El05ma90", "me324");
# SQL query for getting the image
$query = 'SELECT imageType,imageData FROM images WHERE imageId="' . $_GET['id'] . '"';
# Excute query
$result = mysqli_query($link, $query);
# fetch results
$row = mysqli_fetch_assoc($result);
# echo imageData
echo $row['imageData'];

