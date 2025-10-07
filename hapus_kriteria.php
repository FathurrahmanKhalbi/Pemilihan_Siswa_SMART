<?php
include("koneksi.php");

if(isset($_GET['id_kriteria'])) {
    $id = $_GET['id_kriteria'];

    $sql = "DELETE FROM kriteria WHERE id_kriteria = $id";
    $result = mysqli_query($dbcon, $sql);

    if($result) {
        echo "Record deleted successfully";
        header("Location: kriteria.php");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($dbcon);
    }

    mysqli_close($dbcon);
} else {
    echo "Invalid request";
}
?>
