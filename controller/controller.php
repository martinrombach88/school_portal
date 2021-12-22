<?php
require_once("./model/UserManager.php");
require_once("./model/CourseManager.php");

function landing(){
    require("./view/landing.php");
}

// below i have access to the usermanager when the user logs in, i can pass in the 
// $params['userRole']

function login($params){
    $userManager = new UserManager();
    $userConnected = $userManager->logInUser($params['username'], $params['password']);
    if($userConnected){
        header('Location: index.php?action=courseList');
    } else {
        header('Location: index.php');
    }
}

function logout() {
    session_unset();
    session_destroy();
    header('Location: index.php');
}

function courseList(){
    $courseManager = new CourseManager();
    $courses = $courseManager->getCourses();
    require("./view/courseList.php");
}

function userView(){
    $getUsers = new UserManager();
    $users = $getUsers->getUsers();
    require("./view/userView.php");  
}

function userId($id){
    $idManager = new UserManager($id);
    $idManager->delete();
    header("location:index.php?action=userView");
}

function userProfile(){
    $profileUserManager = new UserManager();
    $userProf = $profileUserManager->getUser($_SESSION['userId']);
    require("./view/userProfile.php");
// function userProfile($userId){
//     $userManager = new UserManager();
//     $user = $userManager->getUser($userId);
//     
}
function course($courseid){
    $courseManager = new CourseManager();
    $course = $courseManager->getCourse($courseid);
    require("./view/course.php");
}

function addEditCourseForm($courseid=null){
    if($courseid){
        $courseManager = new CourseManager();
        $course = $courseManager->getCourse($courseid);
    }
    // in order to load the information inside the following view
    require("./view/addEditCourse.php");
}
function addEditCourse($params){
    $courseManager = new CourseManager();
    if(isset($params['edit'])) {
        $course_id = $courseManager->updateCourse($params);
    } else {
        $course_id = $courseManager->addCourse($params);
    }
    header("location:index.php?action=course&courseid=".$course_id);
}

function deleteCourse($courseid){
    $courseManager = new CourseManager();
    $course = $courseManager->delCourse($courseid);
    header("location:index.php?action=courseList");
}

function uploadImage(){
    $userid = $_SESSION['userId'];
    $maxSize = 1300000;
    $valid_extensions = array('jpg', 'jpeg','png');
    $imageTmpName = $_FILES['image']['tmp_name'];
    $imageName = $_FILES['image']['name'];
    $imageSize = $_FILES['image']['size'];
    $imageError = $_FILES['image']['error'];
    $image_sizes = getimagesize($_FILES['image']['tmp_name']);

    if($imageError > 0) {
        throw new Exception("Error during upload");
    } 
    if ($imageSize> $maxSize) {
        throw new Exception( "the size of your file is too big");
    }

    $uploadExtension =  strtolower(substr(strrchr($imageName,"."), 1));
    $relativePath = dirname(__DIR__, 1). "/private/profilePics/";
    $dir  = $relativePath.$userid;
    $imageName = "_profimg."  . $uploadExtension;
    $imageAndId = $userid . "_profimg."  . $uploadExtension;
    $imageLocation = $dir . $imageName;
    move_uploaded_file($imageTmpName, $imageLocation);
    

    $uploadManager = new UserManager();
    $uploadManager->updateImage($userid, $imageAndId);
    header('Location:index.php?action=userProfile');
}


