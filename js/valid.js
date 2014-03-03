

function checkNotEmpty(element, name)
{
    var val = document.getElementById(element).value;
    
    if(val.trim() == '')
    {
        alert(name+" cannot be empty!");
        return false;
    }
    return true;
}

//modified from this post http://stackoverflow.com/questions/6402743/regular-expression-for-mm-dd-yyy-in-javascript
function isGoodDate(dt)
{
    var dts  = dt.split('-')
       ,dateTest = new Date(dts.join('-'));
    return isNaN(dateTest) ? false : true;
}

function checkDate(element, name)
{
    var val = document.getElementById(element).value;
    
    if(val.trim() != '' && val != '0000-00-00' && !isGoodDate(val))//dates are OK blank too
    {
        alert(name+" format should be YYYY-MM-DD");
        return false;
    }
    return true;
}

function checkTime(element, name)
{
    var val = document.getElementById(element).value;
    
    if(val.trim() != '' && !val.match(/^\d{2}:\d{2}:\d{2}$/))//doesn't validate ranges... whatever
    {
        alert(name+" format should be HH:MM:SS");
        return false;
    }
    return true;
    
}
