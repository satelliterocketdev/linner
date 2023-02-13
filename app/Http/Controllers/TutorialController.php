<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Lang;

class TutorialController extends Controller
{
    private $views = [
        [
            'url' => '/',
            'contents' => [
                [
                    'text' => 'tutorial.msg1',
                    'buttons' => ['tutorial.btn1', 'tutorial.btn2']
                ],
                [
                    'text' => 'tutorial.msg2',
                    'buttons' => ['tutorial.btn1', 'tutorial.btn2']
                ]
            ]
        ],
        [
            'url' => '/invitation',
            'contents' => [
                [
                    'text' => 'tutorial.msg3',
                    'buttons' => ['tutorial.btn1', 'tutorial.btn2']
                ],
                [
                    'text' => null,
                    'buttons' => null
                ],
                [
                    'text' => 'tutorial.msg4',
                    'buttons' => ['tutorial.btn1']
                ],
                [
                    'text' => null,
                    'buttons' => null
                ],
                [
                    'text' => 'tutorial.msg5',
                    'buttons' => ['tutorial.btn1']
                ],
                [
                    'text' => null,
                    'buttons' => null
                ],
                [
                    'text' => 'tutorial.msg6',
                    'buttons' => ['tutorial.btn1', 'tutorial.btn2']
                ]
            ]
        ],
        [
            'url' => '/magazine',
            'contents' => [
                [
                    'text' => 'tutorial.msg7',
                    'buttons' => ['tutorial.btn1', 'tutorial.btn2']
                ]
            ]
        ],
        [
            'url' => '/auto_answer_setting',
            'contents' => [
                [
                    'text' => 'tutorial.msg8',
                    'buttons' => ['tutorial.btn1', 'tutorial.btn2']
                ]
            ]
        ],
        [
            'url' => '/stepmail',
            'contents' => [
                [
                    'text' => 'tutorial.msg9',
                    'buttons' => ['tutorial.btn1', 'tutorial.btn2']
                ],
                [
                    'text' => 'tutorial.msg10',
                    'buttons' => ['tutorial.btn1', 'tutorial.btn2']
                ],
                [
                    'text' => null,
                    'buttons' => null
                ],
                [
                    'text' => 'tutorial.msg11',
                    'buttons' => ['tutorial.btn1', 'tutorial.btn2']
                ],
                [
                    'text' => null,
                    'buttons' => null
                ],
                [
                    'text' => 'tutorial.msg12',
                    'buttons' => ['tutorial.btn1', 'tutorial.btn2']
                ],
                [
                    'text' => null,
                    'buttons' => null
                ],
                [
                    'text' => 'tutorial.msg13',
                    'buttons' => ['tutorial.btn1', 'tutorial.btn2']
                ]
            ]
        ],
        [
            'url' => '/accounts',
            'contents' => [
                [
                    'text' => 'tutorial.msg14',
                    'buttons' => ['tutorial.btn1', 'tutorial.btn2']
                ]
            ],
        ],
        [
            'url' => '/accounts_analysis',
            'contents' => [
                [
                    'text' => 'tutorial.msg15',
                    'buttons' => ['tutorial.btn1', 'tutorial.btn2']
                ]
            ]
        ],
        [
            'url' => '/friends',
            'contents' => [
                [
                    'text' => 'tutorial.msg16',
                    'buttons' => ['tutorial.btn1', 'tutorial.btn2']
                ]
            ]
        ],
        [
            'url' => '/deliveries',
            'contents' => [
                [
                    'text' => 'tutorial.msg17',
                    'buttons' => ['tutorial.btn1', 'tutorial.btn2']
                ],
                [
                    'text' => 'tutorial.msg18',
                    'buttons' => ['tutorial.btn3']
                ]
            ]
        ]
    ];
    
    private function runTutorial($index)
    {
        if (!isset($this->views[$index])) {
            $this->skipTutorial();
            return;
        }

        $currentView = $this->views[$index];
        for ($i = 0; $i < count($currentView['contents']); $i++) {
            $currentView['contents'][$i]['text'] = Lang::get($currentView['contents'][$i]['text']);
            for ($j = 0; $j < count($currentView['contents'][$i]['buttons']); $j++) {
                $currentView['contents'][$i]['buttons'][$j] = Lang::get($currentView['contents'][$i]['buttons'][$j]);
            }
        }

        return json_encode($currentView, JSON_UNESCAPED_UNICODE);
    }

    public function getCurrentContents($index)
    {
        return $this->runTutorial($index);
    }

    public function skipTutorial()
    {
        Auth::user()->update(['finished_tutorial' => true]);
    }
}
