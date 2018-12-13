/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function activeLink(i){
    switch(i){
        case 1: 
            $('#bs-example-navbar-collapse-1 ul li:first-child').toggleClass("active");
            break;
        case 2: 
            $('#bs-example-navbar-collapse-1 ul li:nth-child(2)').toggleClass("active");
            break;
        case 3: 
            $('#bs-example-navbar-collapse-1 ul li:nth-child(3)').toggleClass("active");
            break;
        case 4: 
            $('#bs-example-navbar-collapse-1 ul li:nth-child(4)').toggleClass("active");
            break;
        case 5: 
            $('#bs-example-navbar-collapse-1 ul li:nth-child(5)').toggleClass("active");
            break;
        case 6: 
            $('#bs-example-navbar-collapse-1 ul li:nth-child(6)').toggleClass("active");
            break;
    }
}

function loginLink(i){
    if (i == 1)
        $('#login').attr('href', 'Login.php').text('Log In');
    else if (i == 2)
        $('#login').attr('href', 'Logout.php').text('Log Out');
}


$(document).ready(function(){
    $(".dropdown-item").unbind().click(function(){
        window.location = $(this).children().attr("href");
    });
});