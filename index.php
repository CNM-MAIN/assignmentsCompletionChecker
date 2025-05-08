<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'connectionMoodle.php';



$error= 0;
$typeError='';
if($_SERVER['REQUEST_METHOD']== "GET"){
    if (isset($_GET['studentNumber'])) {
        
        $stNumber = test_input($_GET['studentNumber']);
        $stmt = $conn->prepare('SELECT id FROM moodleprod.mdl_user where username = ?');
        if (!$stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        
        $stmt->bind_param("s", $stNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $idStudent= 0;
        while ($row = $result->fetch_assoc()) {
            $idStudent= $row['id'];
        }
        echo $idStudent . "<br>";



$stmt = $conn->prepare('
SELECT 
 mdl_assign_submission.id, assignment, userid, status, mdl_course_modules.course, mdl_assign.name, grade, attemptnumber, latest , mdl_course_modules.id as "ActivityId"
FROM moodleprod.mdl_assign_submission 
INNER JOIN mdl_assign on mdl_assign_submission.assignment = mdl_assign.id 
INNER JOIN mdl_course_modules on mdl_course_modules.instance = mdl_assign.id 
INNER JOIN mdl_modules on mdl_course_modules.module = mdl_modules.id 
where userid = ?
GROUP BY mdl_assign_submission.id 
ORDER BY status DESC

');

if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}

$stmt->bind_param("i", $idStudent);
$stmt->execute();
$result = $stmt->get_result();
var_dump($result);


    } else {
        echo " No 'stnumber' parameter found in GET request.";
    }
}





function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exams checker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>


<body>

    <div class="container mt-5">
        <h2>Exams Checker</h2>

        <form action="" method="GET">
            <input type="text" class="form-control mt-5 w-25" id="StudentNumberInput" name="studentNumber" aria-describedby="stNum" placeholder="Enter student number">
            <button id="updateData" class="btn btn-primary mt-2">Check</button>
        </form>
        <br><br>
        <table class="table table-striped table-bordered table-sm" id="assignmentsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Assignment ID</th>
                    <th>Course name</th>
                    <th>Status</th>
                    <th>Attempt Number</th>
                    <th>Latest</th>
                    <th>Action Button</th>
                    
                </tr>
            </thead>
            <tbody id="dataBody">
           

            <?php 
if (isset($result) && $result->num_rows > 0) : 
    while ($row = $result->fetch_assoc()) :
?>
 <tr>
    <td><?php echo $row['id'] ?></td>
    <td><?php echo $row['assignment'] ?></td>
    <td><?php echo htmlspecialchars($row['name'] , ENT_QUOTES, 'UTF-8')?></td>
    <td><?php echo $row['status'] ?></td>
    <td><?php echo $row['attemptnumber'] ?></td>
    <td><?php echo $row['latest'] ?></td>
    <td><?php echo '<a href="https://online.cnmstudent.com/mod/assign/view.php?id='.$row['ActivityId'].'">' . "Go to assignment". "</a>"?></td>
</tr>
<?php 
    endwhile;
else :
    echo "<td colspan='5'>No data to show</td>";
endif;

?>
     
            </tbody>
        </table>
    </div>

    


<script>
    window.onload =()=>{
        let input= document.querySelector('#StudentNumberInput');
        let button = document.querySelector('#updateData');
        input.addEventListener('input', function (event){
            console.log(event.target.value);
        })
    }

</script>

</body>

</html>