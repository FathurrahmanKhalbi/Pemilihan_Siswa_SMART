<?php
include("koneksi.php");

if (isset($_GET['id_alt'])) {
    $id = $_GET['id_alt'];

    $sqlDeleteRelatedData = "DELETE FROM tabel_nilai WHERE alternatif_id = $id";
    $resultRelatedData = mysqli_query($dbcon, $sqlDeleteRelatedData);

    if ($resultRelatedData) {
        $sql = "DELETE FROM alternatif WHERE id_alt = $id";
        $result = mysqli_query($dbcon, $sql);

        if ($result) {
            echo "Record deleted successfully";
            header("Location: alternatif.php");
            exit();
        } else {
            echo "Error deleting record: " . mysqli_error($dbcon);
        }
    } else {
        echo "Error deleting related data: " . mysqli_error($dbcon);
    }
} else {
    echo "Invalid request";
}
?>
