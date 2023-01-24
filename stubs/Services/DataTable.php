<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class DataTable
{
    protected $prefix;
    protected $model;
//    public function __construct()
//    {
//
//    }
//
    public function generate(string $class, array $headers, array $settings = [], $filters = [], $relationship_loads = [], $queries = [])
    {

        $this->model = $class;
        $this->prefix = class_basename($class);
        $collection = "App\Http\Resources\\" . $this->prefix . "Resource";
//        dd((new ( "App\Http\Resources\\".$collection::collection)($class::all())));
        $query = $class::query();
        is_null($filters) ? $query = $class::query() : $query = $class::filter($filters);

        foreach ($queries as $key => $_query) {
            match ($key) {
                "where" => $query->where($_query),
                "wherein" => $this->loopWhereIn($_query, $query),
                "orwhere" => $this->loopOrWhere($_query, $query),
                "wherejson" => $this->loopWhereJson($_query, $query),
            };
        }
//        dd($query->toSql());
        return [
            "headers" => $this->getheaders($headers),
            "data" => $collection::collection($query->get()->load($relationship_loads)),
            "settings" => $this->getSettings($settings)
        ];
    }

    public function loopWhereIn($_query, $query)
    {
        foreach ($_query as $q) {
            $query->whereIn($q[0], $q[1]);
        }
        return $query;
    }

    public function loopOrWhere($_query, $query)
    {
        foreach ($_query as $q) {
            $query->orWhere($q[0], $q[1]);
        }
        return $query;
    }
    public function loopWhereJson($_query, $query)
    {
        foreach ($_query as $q) {
            $query->whereJsonContains($q[0], $q[1]);
        }
        return $query;
    }
//
//    public function collection()
//    {
//      $this->model::all();
//new baseClass($this->model)
//    }
//
//    public function setPrefix(string $prefix)
//    {
//        $this->prefix=$prefix;
//    }
//    protected function getActions()
//    {
//        Route::getRoutes();
//        $actions=[
//            [
//                "label" => "View",
//                "route_name" => "attendance.field-request.show",
//                "params" => [// the param name in the route is the key and the column value to pass for the param is the value
//                    [
//                        "key" => "field_request",
//                        "value" => "id"
//                    ]
//                ],
//'condition' => [
//'check' => 'status',
//'if' => 'generated',
//'then' => [
//'label' => 'Make Payment',
//'route' => "finance.payroll.create"
//],
//'else' => [
//'label' => 'Generate',
//'route' => "finance.payroll.create"
//],
//],
//                "request_type" => "get",// post get delete
//            ],
//            [
//                "label" => "Edit",
//                "route_name" => "attendance.field-request.edit",
//                "params" => [// the param name in the route is the key and the column value to pass for the param is the value
//                    [
//                        "key" => "field_request",
//                        "value" => "id"
//                    ]
//                ],
//                "request_type" => "get",// post get delete
//            ],
//            [
//                "label" => "Delete",
//                "route_name" => "attendance.field-request.destroy",
//                "params" => [// the param name in the route is the key and the column value to pass for the param is the value
//                    [
//                        "key" => "field_request",
//                        "value" => "id"
//                    ]
//                ],
//                "request_type" => "delete",// post get delete
//            ],
//        ];
//
//        $settings = [
//            "tableTitle" =>
//            "createSection" => [
//                "create" => ["state" => true,
//                    "url" => route('attendance.field-request.create'),
//                    "label" => "Request New"]
//
//            ],
//            "actions" => [
//                "state" => true,
//                "position" => "column-end",// row / column-end /column-start
//                "data" => $actions
//            ],
//            "tableFilter" => [
//                "state" => true,
//                "type" => "button",
//                "activeFilter" => "",
//                "data" => [
//                    [
//                        "label" => "All",
//                        "column" => "",
//                        "key" => ""
//                    ],
//                    [
//                        "label" => "pending",
//                        "column" => "status",
//                        "key" => "pending"
//                    ],
//                    [
//                        "label" => "approved",
//                        "column" => "status",
//                        "key" => 'approved'
//                    ],
//                    [
//                        "label" => "rejected",
//                        "column" => "status",
//                        "key" => 'rejected'
//                    ]
//                ],
//            ],
//            'pageLength' => [
//                'state' => true,
//                'activeLength' => "30",
//                'options' => [
//                    [
//                        'key' => "30",
//                        'label' => "30",
//                    ],
//                    [
//                        'key' => "60",
//                        'label' => "60",
//                    ],
//                    [
//                        'key' => "100",
//                        'label' => "100",
//                    ],
//                    [
//                        'key' => "all",
//                        'label' => "All",
//                    ],]
//            ],
//            "search" => [
//                'state' => false,
//                'placeholder' => "Search",
//                'useButton' => false,
//                "autoSearch" => true,
//            ]
//        ];
//    }
    private function getheaders(array $headers)
    {
        $newHeaders = [];
//            $searchableKey=Str::lower($this->addSlug($header));
//            $searchable = false;
        $slot = false;
        $type = "text";
        $sortable = false;

        foreach ($headers as $header) {

            if (!is_array($header)) {
                $label = Str::title($this->removeSlug($header));
                $key = Str::lower($this->addSlug($header));
            }

            if (is_array($header)) {
                $label = Arr::has($header, 'label') ? Str::title($this->removeSlug($header['label'])) : $label;
                $key = Arr::has($header, 'key') ? Str::lower($this->addSlug($header['key'])) : $key;
                $slot = Arr::has($header, 'slot') ? $header['slot'] : $slot;
                $type = Arr::has($header, 'type') ? $header['type'] : $type;
                $sortable = Arr::has($header, 'sortable') ? $header['sortable'] : $sortable;
            }

            $newHeaders[] = [
                "label" => $label,
                "key" => $key,
                'slot' => $slot,
                'type' => $type,
                'sortable' => $sortable
            ];
        }
        return $newHeaders;
    }

    protected function removeSlug(string $text)
    {
        return Str::replace(['_', '-'], ' ', $text);
    }

    protected function addSlug(string $text)
    {
        return Str::slug($text, '_');
    }

    private function getSettings(array $settings)
    {
        $args = [
            'title',
            'showTitle',
            'showPageLength',
            'enableSearch',
            'enableExports',
            'addNewBtnText',
            'addNewBtnLink',
            'bulkBtnText',
            'bulkBtnLink',
            "actions",
            "actionPosition",
            "actionCondition",
            "pageLength",
            "searchText",
            "autoSearch",
            "searchButton",
            "searchButtonText",
            'exports',
        ];
        return [
            "tableTitle" => [
                "state" => Arr::has($settings, "showTitle") ? $settings['showTitle'] : (Arr::has($settings, "title") || false),
                "label" => Arr::has($settings, "title") ? $settings['title'] : $this->prefix
            ],
            "createSection" => [
                "create" =>
                    [
                        "state" => Arr::has($settings, "addNewBtnLink"),
                        "url" => Arr::has($settings, "addNewBtnLink") ? route($settings['addNewBtnLink']) : "#",
                        "label" => Arr::has($settings, "addNewBtnText") ? $settings['addNewBtnText'] : "Add New"
                    ],
                "bulkCreate" => [
                    "state" => Arr::has($settings, "bulkBtnLink"),
                    "url" => Arr::has($settings, "bulkBtnLink") ? route($settings['bulkBtnLink']) : "#",
                    "label" => Arr::has($settings, "bulkBtnText") ? $settings['bulkBtnText'] : "Bulk Upload"
                ],
            ],
            "actions" => [
                "state" => Arr::has($settings, "actions"),
                "position" => Arr::has($settings, "actionPosition") ? "column-" . $settings["actionPosition"] : "column-" . "end",// row / column-end /column-start
                "data" => $this->generateActions($settings['actions'])
            ],
//            "tableFilter" => "",
            "pageLength" => [
                'state' => Arr::has($settings, "showPageLength") ? $settings['showPageLength'] : (Arr::has($settings, "pageLength") || false),
                'activeLength' => Arr::has($settings, "pageLength") ? $settings['pageLength'] : "30",
                'options' => [
                    [
                        'key' => "30",
                        'label' => "30",
                    ],
                    [
                        'key' => "60",
                        'label' => "60",
                    ],
                    [
                        'key' => "100",
                        'label' => "100",
                    ],
                    [
                        'key' => "all",
                        'label' => "All",
                    ],]
            ],
            "search" => [
                'state' => Arr::has($settings, "enableSearch") ? $settings['enableSearch'] : (Arr::has($settings, "pageLength") || false),
                'placeholder' => Arr::has($settings, "searchText") ? $settings['searchText'] : "Search",
                'useButton' => Arr::has($settings, "searchButton") ? $settings['searchButton'] : false,
                "autoSearch" => Arr::has($settings, "autoSearch") ? $settings['autoSearch'] : true,
            ],
            'exports' => [
                'state' => Arr::has($settings, "enableExports") ? $settings['enableExports'] : (Arr::has($settings, "exports") || false),
                'actions' => Arr::has($settings, "exports") ? $settings['exports'] : []
            ]
        ];
    }

    private function generateActions(mixed $actions)
    {
        $default = [
            "label" => "View",
            "route_name" => "attendance.field-request.show",
            "params" => [// the param name in the route is the key and the column value to pass for the param is the value
                [
                    "key" => "field_request",
                    "value" => "id"
                ]
            ],
            'condition' => [
                'check' => 'status',
                'if' => 'generated',
                'then' => [
                    'label' => 'Make Payment',
                    'route' => "finance.payroll.create"
                ],
                'else' => [
                    'label' => 'Generate',
                    'route' => "finance.payroll.create"
                ],
            ],
            "request_type" => "get",// post get delete
        ];
        $newActions = [];
        if (Arr::isAssoc($actions))
            foreach ($actions as $key => $action) {
                $params = [];
                foreach ($action[2] as $pkey => $param) {
                    $params[] = [
                        "key" => $pkey,
                        "value" => $param
                    ];
                }

                $newActions[] = [
                    "label" => Str::title($key),
                    "request_type" => $action[0],
                    "route_name" => $action[1],
                    "params" => $params,
                    "condition" => sizeof($action) == 4 ? $action[3] : [],
                ];
            }
        return $newActions;
    }

    public function formatStatus($status)
    {
        return '<span class="text-xs font-semibold inline-block text-white py-1 px-2 uppercase rounded ' . $this->colorStatus($status) . '  uppercase last:mr-0 mr-1">
            ' . $this->textStatus($status) . '
            </span>';
    }

    public function textStatus($case)
    {
        if (!is_string($case)) {
            $case = var_export($case, true);
        }
        switch ($case) {
            case "true":
                return 'Yes';
            case 'active':
                return "Active";
            case 'approved':
                return "Approved";
            case "false":
                return 'No';
            case 'inactive':
                return "Inactive";
            case 'rejected':
                return "Rejected";
            case 'pending':
                return "Pending";
            default:
                return "N/A";
        }

    }

    public function colorStatus($case)
    {
        if (!is_string($case)) {
            $case = var_export($case, true);
        }
        switch ($case) {
            case 'approved':
            case 'true':
            case 'active':
                return "bg-emerald-600";
            case 'rejected':
            case 'false':
            case 'inactive':
                return "bg-red-600";
            default:
                return "bg-gray-500";
        }
    }

    public function makeAvatar($model)
    {
        $avatar = $model->avatar;
        $badger_class = Arr::has($model->metadata, 'online_status') ? ($model->metadata['online_status'] == true ? "badger" : "") : "";
        return '<div class="relative h-12 w-12"><img src="' . $avatar . '" class="rounded-full h-12" alt="user"><span class="' . $badger_class . '"> </span></div>';
    }
}
