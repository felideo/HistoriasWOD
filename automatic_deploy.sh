echo "<br>"
echo "******************** Deploy Automatico SWDB ********************<br>"
echo "<br>"

cd "/www/swdb/"
# cp /www/swdb/config.php ~/config.php.swdb.back
git checkout -- .
git fetch --all
git reset --hard origin/master
git pull --rebase
# cp ~/config.php.swdb.back /www/swdb/
# mv config.php.swdb.back config.php

echo "<br>"
echo "<br>"
echo "***************************************************************<br>"
echo "<br>"

echo "********************* 2 Minutos em Produção **********************<br>"
echo "<br>"
