<?php

require "bootstrap.php";
$errorMessage = false;
if(isset($_POST['uname']) && isset($_POST['password'])) 
{
  if(!$userLogin->ValidateLogin($_POST['uname'],$_POST['password']))
  {
    //show error message;
    $errorMessage = true;
  }
}
if($userLogin->checkLogin())
{
  $path = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
  $returnUrl =rawurldecode($path) === rawurldecode("$rootpath/login.php")?"$rootpath/":$_SERVER['HTTP_REFERER'];
  /*echo rawurldecode("$rootpath/login.php")."<br/>";
  echo rawurldecode($_SERVER['HTTP_REFERER'])."<br/>";
  echo $returnUrl;*/
  header("Location: $returnUrl");
  exit();
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" 
integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
<style>
    .gradient-custom {
/* fallback for old browsers */
background: #6a11cb;

/* Chrome 10-25, Safari 5.1-6 */
background: -webkit-linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1));

/* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
background: linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1))
}
</style>
<section class="vh-100 gradient-custom">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card bg-dark text-white" style="border-radius: 1rem;">
          <div class="card-body p-5 text-center">
          
                <form action="login.php" method="post">
                    <div class="mb-md-5 mt-md-4 pb-5">

                    <h2 class="fw-bold mb-2 text-uppercase">Login</h2>
                    <p class="text-white-50 mb-5">Please enter your login and password!</p>

                    <div class="form-outline form-white mb-4">
                        <input type="text" id="uname" name="uname" class="form-control form-control-lg" <?php echo !isset($_POST['uname']) ? '123' : 'value='.$_POST['uname']; ?> />
                        <label class="form-label" for="uname">Email</label>
                    </div>

                    <div class="form-outline form-white mb-4">
                        <input type="password" id="password" name="password" class="form-control form-control-lg" />
                        <label class="form-label" for="password">Password</label>
                    </div>

                    <p class="small mb-5 pb-lg-2"><a class="text-white-50" href="#!">Forgot password?</a></p>
                    <button class="btn btn-outline-light btn-lg px-5" id="btLogin">Login</button>
                    <div class="d-flex justify-content-center text-center mt-4 pt-1">
                        <a href="#!" class="text-white"><i class="fab fa-facebook-f fa-lg"></i></a>
                        <a href="#!" class="text-white"><i class="fab fa-twitter fa-lg mx-4 px-2"></i></a>
                        <a href="#!" class="text-white"><i class="fab fa-google fa-lg"></i></a>
                    </div>
                </form>
            </div>

           
            <div>
              <p class="mb-0">Don't have an account? <a href="#!" class="text-white-50 fw-bold">Sign Up</a>
              </p>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>