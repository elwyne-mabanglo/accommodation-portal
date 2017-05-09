<?php
# deleteImage - Use to delete selected images
#
# Get image Id from url
$image_id = $_GET['image_id'];
# Setup connection
$db = mysqli_connect("mysql.cms.gre.ac.uk", "me324", "12El05ma90", "me324");
# SQL query for deleting
$sql = "DELETE FROM images WHERE imageId=" . $image_id;
# Excute query
$sth = $db->query($sql);
# Redirect to View Properties Page
header("Location: viewProperties.php");
# Prevent unexpected behaviour after redirection
die("Redirecting to: viewProperties.php");
# Close connection
$conn->close();
