<?php
if (isset($_GET['id'])) {
    include "integration/integration-details.php";
} else if (isset($_GET['new'])) {
    include "integration/integration-new.php";
} else {
    include "integration/integration-list.php";
}
