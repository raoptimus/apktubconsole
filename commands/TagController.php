<?php
namespace app\commands;

use app\models\video\Tag;
use app\models\video\Video;
use RuntimeException;
use yii\console\Controller;
use Yii;

class TagController extends Controller
{
    /**
     * This command will send message to a new member
     * @return int Exit Code
     */
    public function actionFixTags()
    {
        $ret = Tag::getCollection()->createIndex(['Title.Quote' => 1, 'Title.Language' => 1], ['background' => true, 'unique' => true, 'sparse' => true]);
        if (!$ret) {
            throw new RuntimeException("Cant create db index 'Title.Quote'");
        }
        while (true) {
            $videos = Video::find()->where([
                'Tags.Language' => ['$exists' => false],
            ])->limit(1000);

            $c = $videos->count();
            echo  $c . " left \n";
            if ($c == 0) {
                break;
            }

            $videos = $videos->all();
            if (empty($videos)) {
                continue;
            }
            echo "begining\n";

            foreach ($videos as $video) {
                /**
                 * @var Video $video
                 */
                foreach ($video->Tags as $rawTag) {
                    /**
                     * @var Tag $tag
                     */
                    $tag = Tag::find()->where([
                        'Title.Quote' => $rawTag
                    ])->one();

                    if ($tag) {
                        $tag->incVideoCount();
                        continue;
                    }
                    $tag = new Tag();
                    $tag->VideoCount = 1;
                    $tag->Title = [
                        [
                            "Language" => "ru",
                            "Quote" => mb_strtolower($rawTag, "UTF-8"),
                        ]
                    ];
                    if (!$tag->save()) {
                        throw new RuntimeException(implode("", $tag->getErrors()));
                    }
                }

                $video->Tags = [
                    [
                        'Language' => 'ru',
                        'Tags' => $video->Tags
                    ]
                ];

                if (!$video->save(false, ['Tags'])) {
                    throw new RuntimeException(implode("", $video->getErrors()));
                }
            }
        }
        echo "finish";
    }

    public function actionFillTagsCollection() {
        $ret = Video::getCollection()->createIndex(['Tags.Tags' => 1], ['background' => true, 'sparse' => true]);
        if (!$ret) {
            throw new RuntimeException("Cant create db index 'Title.Quote'");
        }

        $tags = Video::getCollection()->distinct('Tags.Tags');
        print count($tags) . ' left' . "\n";
        $i = 0;
        foreach ($tags as $tag_name) {
            if (!($i%100)) {
                print count($tags) - $i . ' left' . "\n";
            }

            $count = Video::find()->where(['Tags.Tags' => $tag_name])->count();

            $tag = new Tag();
            $tag->VideoCount = $count;
            $tag->Title = [
                [
                    "Language" => "ru",
                    "Quote" => mb_strtolower($tag_name, "UTF-8"),
                ]
            ];
            if (!$tag->save()) {
                throw new RuntimeException(implode("", $tag->getErrors()));
            }
            $i++;
        }
    }
}
