<?php
include_once "config.php";
$connnection = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
if(!$connnection){
    throw new Exception("Cannot connect to database");
}

$query = "SELECT * FROM tasks WHERE complete = 0 ORDER BY date DESC";
$result = mysqli_query($connnection, $query);

$completeTasksQuery = "SELECT * FROM tasks WHERE complete = 1 ORDER BY date DESC";
$resultCompleteTasks = mysqli_query($connnection, $completeTasksQuery);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.css">
    <title>Todo/Tasks</title>
    <style>
        body{
            margin-top: 30px;
        }
        #main{
            padding: 0px 150px 0px 150px;
        }
        #action{
            width:150px;
        }
    </style>
    
</head>
<body>
    <div class="container" id="main">
        <h1>Tasks Manager</h1>
        <p>This is a sample project for managing our daily tasks. We're going to use HTML, CSS, PHP, JavaScript and MySQL for the project</p>

        <?php
            if(mysqli_num_rows($resultCompleteTasks) > 0){
                ?>
                <h4>Complete Tasks</h4>
                <table>
                    <thead>
                        <tr>
                            <th></th>
                            <th>Id</th>
                            <th>Tasks</th>
                            <th>Data</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                <tbody>
                <?php
                while($cdata = mysqli_fetch_assoc($resultCompleteTasks)){
                    $timestamp = strtotime($cdata['date']);
                    $cdate = date("jS M Y", $timestamp);
                    ?>
                    <tr>
                       <td><input class="lebel-inline" type="checkbox" value="<?php echo $cdata['id']; ?>"></td>
                       <td><?php echo $cdata['id']; ?></td>
                       <td><?php echo $cdata['task'] ?></td>
                       <td><?php echo $cdate; ?></td>
                       <td><a class="delete" data-taskid="<?php echo $cdata['id'] ?>" href="#">Delete</a> | <a class="incomplete" data-taskid="<?php echo $cdata['id'] ?>" href="#">Mark Incomplete</a></td>
                   </tr>
                   <?php
                }
                ?>
                </tbody>
            </table>
            <p>...</p>
                <?php
            }
            ?>

        <?php
            if(mysqli_num_rows($result)==0){
                ?>
                <p>No Tesk Found</p>
            <?php    
            }else{
            ?>
            <h4>Upcoming Tasks</h4>
            <form action="tasks.php" method="POST">
                <table>
                    <thead>
                        <tr>
                            <th></th>
                            <th>Id</th>
                            <th>Tasks</th>
                            <th>Data</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while($data = mysqli_fetch_assoc($result)){
                            $timestamp = strtotime($data['date']);
                            $date = date("jS M Y", $timestamp);
                        ?>
                        <tr>
                            <td><input name="taskids[]" class="lebel-inline" type="checkbox" value="<?php echo $data['id']; ?>"></td>
                            <td><?php echo $data['id']; ?></td>
                            <td><?php echo $data['task'] ?></td>
                            <td><?php echo $date; ?></td>
                            <td><a class="delete" data-taskid="<?php echo $data['id'] ?>" href="#">Delete</a> | <a class="complete" data-taskid="<?php echo $data['id']; ?>"href="#">Complete</a></td>
                        </tr>
                        <?php
                        }
                        mysqli_close($connnection);
                        ?>
                    </tbody>
                </table>
                <select name="action" id="action">
                    <option value="0">With Selected</option>
                    <option value="bulkdelete">Delete</option>
                    <option value="bulkcomplete">Mark As Complete</option>
                </select>
                <input class="button-primary" id="bulksubmit" type="submit" value="Submit">
            </form>
        <?php
            }
        ?>
        <p>...</p>
        <h4>Add Tasks</h4>
        <form action="tasks.php" method="post">
            <fieldset>
                <?php
                    $added = $_GET['added'] ?? '';
                    if($added){
                        echo "<p>Task Successfully Added</p>";
                    }
                ?>
                <label for="task">Task</label>
                <input type="text" placeholder="Task Details" id="task" name="task">
                <label for="date">Date</label>
                <input type="text" placeholder="Task Date" id="date" name="date">

                <input class="button-primary" type="submit" value="Add Task">
                <input type="hidden" name="action" value="add">
            </fieldset>
        </form>
    </div>
    <form action="tasks.php" method="post" id="completeform">
        <input type="hidden" id="caction" name="action" value="complete">
        <input type="hidden" id="taskid" name="taskid">
    </form>
    <form action="tasks.php" method="post" id="deleteform">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" id="dtaskid" name="taskid">
    </form>
    <form action="tasks.php" method="post" id="incompleteform">
        <input type="hidden" name="action" value="incomplete">
        <input type="hidden" id="itaskid" name="taskid">
    </form>
</body>
<script src="https://code.jquery.com/jquery-3.6.3.slim.min.js"></script>
<script>
    ;(function($){
        $(document).ready(function(){
            $(".complete").on('click',function(){
                var id = $(this).data("taskid");
                // alert(id);
                $("#taskid").val(id);
                $("#completeform").submit();
            });
            $(".delete").on('click',function(){
                if(confirm("Are you sure to delete this task")){
                    var id = $(this).data("taskid");
                    // alert(id);
                    $("#dtaskid").val(id);
                    $("#deleteform").submit();
                }
            });
            $(".incomplete").on('click',function(){
                var id = $(this).data("taskid");
                // alert(id);
                $("#itaskid").val(id);
                $("#incompleteform").submit();
            });
            $("#bulksubmit").on("click",function(){
                if($("#action").val()=='bulkdelete'){
                    if(!confirm("Are you sure to delete?")){
                        return false;
                    }
                }
            });
        });
    })(jQuery);
</script>
</html>

