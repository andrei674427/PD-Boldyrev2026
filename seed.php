<?php
require 'config/database.php';
use Faker\Factory;

$faker = Factory::create('ru_RU');

echo "🚀 Наполнение базы...\n";

/* --- Пользователи --- */
$userIds = [];

$adminPass = password_hash('admin123', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
$stmt->execute(['Администратор', 'admin@fansite.ru', $adminPass]);
$userIds[] = $pdo->lastInsertId();

for ($i=0; $i<10; $i++) {
    $stmt = $pdo->prepare("INSERT INTO users (username,email,password) VALUES (?, ?, ?)");
    $stmt->execute([$faker->name, $faker->unique()->safeEmail, password_hash('123456', PASSWORD_DEFAULT)]);
    $userIds[] = $pdo->lastInsertId();
}

/* --- Персонажи --- */
$characters = [
    ['Маша Васнецова','Мирослава Карпович','Старшая сестра, модница.'],
    ['Даша Васнецова','Анастасия Сиваева','Умная и рассудительная.'],
    ['Женя Васнецова','Дарья Мельникова','Энергичная спортсменка.'],
    ['Галина Сергеевна','Елизавета Арзамасова','Отличница, гений семьи.'],
    ['Пуговка','Катя Старшова','Младшая, милая и наивная.'],
    ['Сергей Васнецов','Андрей Леонов','Отец семейства, психотерапевт.']
];
$characterIds = [];
foreach($characters as $c){
    $stmt = $pdo->prepare("INSERT INTO characters (name, actress, description, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$c[0], $c[1], $c[2], 'default.jpg']);
    $characterIds[] = $pdo->lastInsertId();
}

/* --- Сезоны и серии --- */
$seasonIds=[];
for($s=1;$s<=3;$s++){
    $stmt = $pdo->prepare("INSERT INTO seasons (season_number, year, description) VALUES (?, ?, ?)");
    $stmt->execute([$s, 2007+$s, "Описание сезона $s"]);
    $seasonId = $pdo->lastInsertId();
    $seasonIds[] = $seasonId;

    for($e=1;$e<=10;$e++){
        $stmt = $pdo->prepare("INSERT INTO episodes (season_id, episode_number, title, description, air_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$seasonId,$e,$faker->sentence(4),$faker->realText(200),$faker->date()]);
    }
}

/* --- Новости --- */
$newsIds=[];
for($i=0;$i<20;$i++){
    $stmt = $pdo->prepare("INSERT INTO news (title, content, image, user_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$faker->sentence(6),$faker->realText(400),'news.jpg',$userIds[array_rand($userIds)]]);
    $newsIds[] = $pdo->lastInsertId();
}

/* --- Комментарии --- */
for($i=0;$i<50;$i++){
    $stmt = $pdo->prepare("INSERT INTO comments (user_id, news_id, character_id, content) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $userIds[array_rand($userIds)],
        $newsIds[array_rand($newsIds)],
        $characterIds[array_rand($characterIds)],
        $faker->realText(150)
    ]);
}

echo "✅ База успешно заполнена!\n";