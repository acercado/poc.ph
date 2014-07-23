//dom ready
$(document).ready(function () {
    if($('.result-filter input[type="checkbox"]').length > 0){
        $('.result-filter input[type="checkbox"]').checkbox({
          checkedClass : 'fa-check-square',
          uncheckedClass : 'fa-square',
          buttonStyle: 'btn-link btn-lg',
        });
      }

    $('#banksSlider').carousel({
        interval: 10000
    })
    
    $('#banksSlider').on('slid.bs.carousel', function() {
        //alert("slid");
    });


   /* $('.result-container').on('mouseenter', '.cc-result', function(e) {
        $(this).find('.result-highlight').css('background', '#f4c18b');

        //$(this).prev().css('border-bottom', '1px solid #F7982C');
    });*/

    $('.result-container').on('mouseleave', '.cc-result', function(e) {
        $(this).find('.result-highlight').css('background', '#d6f0f7');

        //$(this).prev().css('border-bottom', '1px solid #999');
    });

    //Add Hover effect to menus
    $('ul.nav li.dropdown').hover(function() {
      if(jqUpdateSize()){
        $(this).find('.dropdown-menu').stop(true, true).show();
      }
      $(this).addClass("dropdown-active");
      
    }, function() {
      if(jqUpdateSize()){
        $(this).find('.dropdown-menu').stop(true, true).hide();
      }  
      $(this).removeClass("dropdown-active");
    });

    $(".nav li a").click(function(){
      if(jqUpdateSize()){
        window.location = $(this).attr("href");
      }
    });
    
});




//functions
function IsEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function jqUpdateSize(){
    // Get the dimensions of the viewport
    var width = $(window).width();
    var height = $(window).height();

    if(width >= 780){
      return true;
    }else{
      return false;
    }
}

function isPhoneNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if(charCode == 43){ //if + sign
      return true;
    } else if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function getRandomInt() {
    min = 0;
    max = j_color.length - 1;
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function isNotEmpty(v){
  var v = $.trim(v);
  return v != null && v != "N/A" && v != "" && v != "No" && v != 0 && v != "undefined" && v != "NaN" && v != "NA" && v != " " && v != "n/a" && v != "false" && v != "FALSE";
}

function open_tab(url){
  var win=window.open(url, '_blank');
  win.focus();
}

function PMT(i, n, p) {
    return i * p * Math.pow((1 + i), n) / (1 - Math.pow((1 + i), n));
}

function RATE(paymentsPerYear, paymentAmount, presentValue, futureValue, dueEndOrBeginning, interest){
    if (interest == null)
        interest = 0.01;

    if (futureValue == null)
        futureValue = 0;

    if (dueEndOrBeginning == null)
        dueEndOrBeginning = 0;

    var FINANCIAL_MAX_ITERATIONS = 128;//Bet accuracy with 128
    var FINANCIAL_PRECISION = 0.0000001;//1.0e-8

    var y, y0, y1, x0, x1 = 0, f = 0, i = 0;
    var rate = interest;
    if (Math.abs(rate) < FINANCIAL_PRECISION)
    {
        y = presentValue * (1 + paymentsPerYear * rate) + paymentAmount * (1 + rate * dueEndOrBeginning) * paymentsPerYear + futureValue;
    }
    else
    {
        f = Math.exp(paymentsPerYear * Math.log(1 + rate));
        y = presentValue * f + paymentAmount * (1 / rate + dueEndOrBeginning) * (f - 1) + futureValue;
    }
    y0 = presentValue + paymentAmount * paymentsPerYear + futureValue;
    y1 = presentValue * f + paymentAmount * (1 / rate + dueEndOrBeginning) * (f - 1) + futureValue;

    // find root by Newton secant method
    i = x0 = 0.0;
    x1 = rate;
    while ((Math.abs(y0 - y1) > FINANCIAL_PRECISION)
        && (i < FINANCIAL_MAX_ITERATIONS))
    {
        rate = (y1 * x0 - y0 * x1) / (y1 - y0);
        x0 = x1;
        x1 = rate;

        if (Math.abs(rate) < FINANCIAL_PRECISION)
        {
            y = presentValue * (1 + paymentsPerYear * rate) + paymentAmount * (1 + rate * dueEndOrBeginning) * paymentsPerYear + futureValue;
        }
        else
        {
            f = Math.exp(paymentsPerYear * Math.log(1 + rate));
            y = presentValue * f + paymentAmount * (1 / rate + dueEndOrBeginning) * (f - 1) + futureValue;
        }

        y0 = y1;
        y1 = y;
        ++i;
    }
    return rate;
}