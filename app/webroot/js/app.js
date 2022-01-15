function getAjaxParam()
{
    var dd = new Date();
    return dd.getHours().toString()
         + dd.getMinutes().toString()
         + dd.getSeconds().toString()
         + dd.getMilliseconds();
}
