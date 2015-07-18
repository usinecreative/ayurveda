/**
 * Created by .klr on 18/07/2015.
 */

//antispam mailto
function dolink(ad){
    link = 'mailto:' + ad.replace(/\.\..+t\.\./,"@");
    return link;
}
