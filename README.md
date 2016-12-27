# phpfunctions
usage:  
  
functions  
{{ array_rand(array, numitems) }} - select random numitems from array  
{{ str_replace(search, replace, subject) }} replaces search whith replace in subject  
{{ filesize(file) }} returns filesize  
{{ videoinfo(videolink) }} returns preview image from youtube videolink  
{{ pdfpre(pdffilename, width, height) }} returns image with pdf preview of 1st page (thanks to pdfPreview by blockmurder for Bolt 2)  
filters  
{{ array|suffle }} randomizing array  
{{ string|url_decode }} decode sting from url to normal format  
