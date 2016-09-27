<h1>Hello World!</h1>
<h2>Привет Мир!</h2>


<form method="get" action='?r=site/paramhandler'>
    <input type="hidden" name="r" value="site/paramhandler"/>
    <input type="text" name="first" value="Hello"/>
    <input type="text" name="secont" value="World"/>
    <input type="submit"/>
</form>

<form method="post" action='?r=site/paramhandler'>
    <input type="hidden" name="r" value="site/paramhandler"/>
    <input type="text" name="third" value="Привет"/>
    <input type="text" name="fourth" value="Мир"/>
    <input type="submit"/>
</form>


<br/>

<img src="/www/img/test.jpg"/>