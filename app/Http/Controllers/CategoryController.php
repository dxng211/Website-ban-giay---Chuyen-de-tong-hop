<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {}

    public function listSizeCharts(): View
    {
        if (!method_exists($this->categoryService, 'listSizeCharts')) {
            if (Schema::hasTable('size_chart_groups')) {
                $query = DB::table('size_chart_groups');
                if (Schema::hasColumn('size_chart_groups', 'id')) {
                    $query->orderBy('id');
                } elseif (Schema::hasColumn('size_chart_groups', 'chart_key')) {
                    $query->orderBy('chart_key');
                } elseif (Schema::hasColumn('size_chart_groups', 'key')) {
                    $query->orderBy('key');
                } elseif (Schema::hasColumn('size_chart_groups', 'chart_name')) {
                    $query->orderBy('chart_name');
                }
                $groups = $query->get();
            } else {
                $groups = collect();
            }
        } else {
            $groups = call_user_func([$this->categoryService, 'listSizeCharts']);
        }

        return view('admin.size-charts.index', compact('groups'));
    }

    public function showCreateSizeChartForm(): View
    {
        if (request()->is('admin/categories/create')) {
            $category = new Category();

            return view('admin.categories.edit', compact('category'));
        }

        return view('admin.size-charts.create');
    }

    public function createSizeChart(Request $request): RedirectResponse
    {
        if ($request->is('admin/categories') || $request->is('admin/categories/*')) {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'slug' => ['nullable', 'string', 'max:255'],
                'group_type' => ['nullable', 'string', 'max:50'],
                'size_chart_key' => ['nullable', 'string', 'max:100'],
                'description' => ['nullable', 'string'],
                'is_active' => ['nullable'],
            ]);

            $payload = ['name' => $data['name']];
            if (Schema::hasColumn('categories', 'slug')) {
                $payload['slug'] = $data['slug'] ?: Str::slug($data['name']);
            }
            if (Schema::hasColumn('categories', 'group_type')) {
                $payload['group_type'] = $data['group_type'] ?? 'shoe';
            }
            if (Schema::hasColumn('categories', 'size_chart_key')) {
                $payload['size_chart_key'] = $data['size_chart_key'] ?: null;
            }
            if (Schema::hasColumn('categories', 'description')) {
                $payload['description'] = $data['description'] ?? null;
            }
            if (Schema::hasColumn('categories', 'is_active')) {
                $payload['is_active'] = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
            }

            Category::create($payload);

            return redirect()->route('admin.categories.index')
                ->with('success', 'Thêm danh mục thành công.');
        }

        if (method_exists($this->categoryService, 'createSizeChart')) {
            call_user_func([$this->categoryService, 'createSizeChart'], $request->all());
        } else {
            $input = $request->all();
            $chartKey = trim((string) ($input['chart_key'] ?? $input['key'] ?? ''));
            $title = trim((string) ($input['chart_name'] ?? $input['name'] ?? $input['title'] ?? $chartKey));

            if (Schema::hasTable('size_chart_groups') && $chartKey !== '') {
                $groupData = [];
                if (Schema::hasColumn('size_chart_groups', 'chart_key')) {
                    $groupData['chart_key'] = $chartKey;
                } elseif (Schema::hasColumn('size_chart_groups', 'key')) {
                    $groupData['key'] = $chartKey;
                }

                if (Schema::hasColumn('size_chart_groups', 'chart_name')) {
                    $groupData['chart_name'] = $title;
                } elseif (Schema::hasColumn('size_chart_groups', 'name')) {
                    $groupData['name'] = $title;
                } elseif (Schema::hasColumn('size_chart_groups', 'title')) {
                    $groupData['title'] = $title;
                }

                if (Schema::hasColumn('size_chart_groups', 'size_column_label')) {
                    $groupData['size_column_label'] = trim((string) ($input['size_column_label'] ?? $input['col1_label'] ?? 'Size')) ?: 'Size';
                }

                if (Schema::hasColumn('size_chart_groups', 'reference_column_label')) {
                    $groupData['reference_column_label'] = trim((string) ($input['reference_column_label'] ?? $input['col2_label'] ?? 'Chiều dài / Ghi chú')) ?: 'Chiều dài / Ghi chú';
                }

                if (Schema::hasColumn('size_chart_groups', 'description') && isset($input['description'])) {
                    $groupData['description'] = $input['description'];
                }

                if (Schema::hasColumn('size_chart_groups', 'is_active')) {
                    $groupData['is_active'] = 1;
                }

                if (Schema::hasColumn('size_chart_groups', 'created_at')) {
                    $groupData['created_at'] = now();
                }
                if (Schema::hasColumn('size_chart_groups', 'updated_at')) {
                    $groupData['updated_at'] = now();
                }

                if (!empty($groupData)) {
                    $keyCol = Schema::hasColumn('size_chart_groups', 'chart_key') ? 'chart_key' : 'key';
                    $updateValues = $groupData;
                    unset($updateValues[$keyCol], $updateValues['created_at']);

                    DB::table('size_chart_groups')->updateOrInsert(
                        [$keyCol => $chartKey],
                        $updateValues
                    );
                }
            }

            $rows = $input['items'] ?? $input['sizes'] ?? $input['rows'] ?? [];
            if (Schema::hasTable('size_charts') && is_array($rows) && $chartKey !== '') {
                $itemKeyCol = Schema::hasColumn('size_charts', 'chart_key')
                    ? 'chart_key'
                    : (Schema::hasColumn('size_charts', 'group_key') ? 'group_key' : null);

                if ($itemKeyCol !== null) {
                    DB::table('size_charts')->where($itemKeyCol, $chartKey)->delete();
                }

                foreach ($rows as $idx => $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    $sizeVal = trim((string) (
                        $row['size_label'] ?? $row['size'] ?? $row['label'] ?? $row['name'] ?? ''
                    ));
                    $noteVal = trim((string) (
                        $row['reference_value'] ?? $row['range'] ?? $row['note'] ?? $row['description'] ?? $row['foot_length'] ?? $row['length'] ?? ''
                    ));
                    if ($sizeVal === '' && $noteVal === '') {
                        continue;
                    }

                    $itemData = [];
                    if ($itemKeyCol !== null) {
                        $itemData[$itemKeyCol] = $chartKey;
                    }

                    if (Schema::hasColumn('size_charts', 'size_label')) {
                        $itemData['size_label'] = $sizeVal;
                    } elseif (Schema::hasColumn('size_charts', 'size')) {
                        $itemData['size'] = $sizeVal;
                    } elseif (Schema::hasColumn('size_charts', 'name')) {
                        $itemData['name'] = $sizeVal;
                    }

                    if ($noteVal !== '') {
                        if (Schema::hasColumn('size_charts', 'reference_value')) {
                            $itemData['reference_value'] = $noteVal;
                        } elseif (Schema::hasColumn('size_charts', 'note')) {
                            $itemData['note'] = $noteVal;
                        } elseif (Schema::hasColumn('size_charts', 'description')) {
                            $itemData['description'] = $noteVal;
                        } elseif (Schema::hasColumn('size_charts', 'foot_length')) {
                            $itemData['foot_length'] = $noteVal;
                        }
                    }

                    $minVal = 0.0;
                    if (Schema::hasColumn('size_charts', 'reference_min')) {
                        $rawMin = $row['reference_min'] ?? null;
                        if ($rawMin === null || $rawMin === '') {
                            preg_match('/-?\d+(?:[.,]\d+)?/', $noteVal, $m);
                            $rawMin = isset($m[0]) ? str_replace(',', '.', $m[0]) : 0;
                        }
                        $minVal = is_numeric($rawMin) ? (float) $rawMin : 0.0;
                        $itemData['reference_min'] = $minVal;
                    }

                    if (Schema::hasColumn('size_charts', 'reference_max')) {
                        $rawMax = $row['reference_max'] ?? null;
                        if ($rawMax !== null && $rawMax !== '' && is_numeric(str_replace(',', '.', (string) $rawMax))) {
                            $itemData['reference_max'] = (float) str_replace(',', '.', (string) $rawMax);
                        } else {
                            preg_match_all('/-?\d+(?:[.,]\d+)?/', $noteVal, $matches);
                            if (!empty($matches[0]) && count($matches[0]) >= 2) {
                                $itemData['reference_max'] = (float) str_replace(',', '.', end($matches[0]));
                            } else {
                                $itemData['reference_max'] = $minVal;
                            }
                        }
                    }

                    if (Schema::hasColumn('size_charts', 'sort_order')) {
                        $sortRaw = $row['sort_order'] ?? $row['sort'] ?? null;
                        $itemData['sort_order'] = ($sortRaw !== null && $sortRaw !== '') ? (int) $sortRaw : (int) $idx;
                    }
                    if (Schema::hasColumn('size_charts', 'created_at')) {
                        $itemData['created_at'] = now();
                    }
                    if (Schema::hasColumn('size_charts', 'updated_at')) {
                        $itemData['updated_at'] = now();
                    }

                    if (!empty($itemData)) {
                        DB::table('size_charts')->insert($itemData);
                    }
                }
            }
        }

        if ($request->headers->get('referer') && str_contains($request->headers->get('referer'), '/admin/categories')) {
            return redirect()->route('admin.categories.index', ['chart_key' => $chartKey ?? null])
                ->with('success', 'Đã lưu nhóm bảng size và cập nhật bảng xem trước.');
        }

        return redirect()->route('admin.size-charts.index')
            ->with('success', 'Tạo nhóm bảng size thành công.');
    }

    public function showEditSizeChartForm(string $key): View
    {
        $group = method_exists($this->categoryService, 'getSizeChart')
            ? $this->categoryService->getSizeChart($key)
            : (Schema::hasTable('size_chart_groups')
                ? DB::table('size_chart_groups')
                    ->where(Schema::hasColumn('size_chart_groups', 'chart_key') ? 'chart_key' : 'key', $key)
                    ->first()
                : null);

        return view('admin.size-charts.edit', compact('group', 'key'));
    }

    public function updateSizeChart(Request $request, string $key): RedirectResponse
    {
        if ($request->is('admin/categories/*') && $request->isMethod('delete')) {
            $category = Category::findOrFail((int) $key);

            if (Schema::hasTable('products') && DB::table('products')->where('category_id', $category->id)->exists()) {
                return redirect()->route('admin.categories.index')
                    ->with('error', 'Không thể xóa danh mục này vì đang có sản phẩm sử dụng.');
            }

            if (Schema::hasColumn('categories', 'parent_id') && Category::query()->where('parent_id', $category->id)->exists()) {
                return redirect()->route('admin.categories.index')
                    ->with('error', 'Không thể xóa danh mục này vì vẫn còn danh mục con trực thuộc.');
            }

            $category->delete();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Đã xóa danh mục.');
        }

        if ($request->is('admin/categories/*')) {
            $category = Category::findOrFail((int) $key);
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'slug' => ['nullable', 'string', 'max:255'],
                'group_type' => ['nullable', 'string', 'max:50'],
                'size_chart_key' => ['nullable', 'string', 'max:100'],
                'description' => ['nullable', 'string'],
                'is_active' => ['nullable'],
            ]);

            $payload = ['name' => $data['name']];
            if (Schema::hasColumn('categories', 'slug')) {
                $payload['slug'] = $data['slug'] ?: Str::slug($data['name']);
            }
            if (Schema::hasColumn('categories', 'group_type')) {
                $payload['group_type'] = $data['group_type'] ?? ($category->group_type ?? 'shoe');
            }
            if (Schema::hasColumn('categories', 'size_chart_key')) {
                $payload['size_chart_key'] = $data['size_chart_key'] ?: null;
            }
            if (Schema::hasColumn('categories', 'description')) {
                $payload['description'] = $data['description'] ?? null;
            }
            if (Schema::hasColumn('categories', 'is_active')) {
                $payload['is_active'] = filter_var($data['is_active'] ?? ($category->is_active ?? true), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
            }

            $category->update($payload);

            return redirect()->route('admin.categories.index')
                ->with('success', 'Cập nhật danh mục thành công.');
        }

        $input = $request->all();
        $chartKey = trim((string) ($input['chart_key'] ?? $key));
        $title = trim((string) ($input['chart_name'] ?? $input['title'] ?? $input['name'] ?? $chartKey));

        if (method_exists($this->categoryService, 'updateSizeChart')) {
            call_user_func([$this->categoryService, 'updateSizeChart'], $chartKey, $input);
        }

        if (Schema::hasTable('size_chart_groups') && $chartKey !== '') {
            $keyCol = Schema::hasColumn('size_chart_groups', 'chart_key') ? 'chart_key' : 'key';
            $updateValues = [];

            if (Schema::hasColumn('size_chart_groups', 'chart_name')) {
                $updateValues['chart_name'] = $title;
            } elseif (Schema::hasColumn('size_chart_groups', 'name')) {
                $updateValues['name'] = $title;
            } elseif (Schema::hasColumn('size_chart_groups', 'title')) {
                $updateValues['title'] = $title;
            }

            if (Schema::hasColumn('size_chart_groups', 'size_column_label')) {
                $updateValues['size_column_label'] = trim((string) ($input['size_column_label'] ?? $input['col1_label'] ?? 'Size')) ?: 'Size';
            }

            if (Schema::hasColumn('size_chart_groups', 'reference_column_label')) {
                $updateValues['reference_column_label'] = trim((string) ($input['reference_column_label'] ?? $input['col2_label'] ?? 'Chiều dài / Ghi chú')) ?: 'Chiều dài / Ghi chú';
            }

            if (Schema::hasColumn('size_chart_groups', 'updated_at')) {
                $updateValues['updated_at'] = now();
            }

            if (!empty($updateValues)) {
                $insertValues = array_merge([$keyCol => $chartKey], $updateValues);
                if (Schema::hasColumn('size_chart_groups', 'created_at')) {
                    $insertValues['created_at'] = now();
                }
                DB::table('size_chart_groups')->updateOrInsert([$keyCol => $chartKey], $updateValues);
                DB::table('size_chart_groups')->where($keyCol, $chartKey)->update($updateValues);
            }
        }

        $rows = $input['items'] ?? $input['sizes'] ?? $input['rows'] ?? null;
        if (Schema::hasTable('size_charts') && is_array($rows) && $chartKey !== '') {
            $itemKeyCol = Schema::hasColumn('size_charts', 'chart_key')
                ? 'chart_key'
                : (Schema::hasColumn('size_charts', 'group_key') ? 'group_key' : null);

            if ($itemKeyCol !== null) {
                DB::table('size_charts')->where($itemKeyCol, $chartKey)->delete();
            }

            foreach ($rows as $idx => $row) {
                if (!is_array($row)) {
                    continue;
                }
                $sizeVal = trim((string) (
                    $row['size_label'] ?? $row['size'] ?? $row['label'] ?? $row['name'] ?? ''
                ));
                $noteVal = trim((string) (
                    $row['reference_value'] ?? $row['range'] ?? $row['note'] ?? $row['description'] ?? $row['foot_length'] ?? $row['length'] ?? ''
                ));
                if ($sizeVal === '' && $noteVal === '') {
                    continue;
                }

                $itemData = [];
                if ($itemKeyCol !== null) {
                    $itemData[$itemKeyCol] = $chartKey;
                }

                if (Schema::hasColumn('size_charts', 'size_label')) {
                    $itemData['size_label'] = $sizeVal;
                } elseif (Schema::hasColumn('size_charts', 'size')) {
                    $itemData['size'] = $sizeVal;
                } elseif (Schema::hasColumn('size_charts', 'name')) {
                    $itemData['name'] = $sizeVal;
                }

                if ($noteVal !== '') {
                    if (Schema::hasColumn('size_charts', 'reference_value')) {
                        $itemData['reference_value'] = $noteVal;
                    } elseif (Schema::hasColumn('size_charts', 'note')) {
                        $itemData['note'] = $noteVal;
                    } elseif (Schema::hasColumn('size_charts', 'description')) {
                        $itemData['description'] = $noteVal;
                    } elseif (Schema::hasColumn('size_charts', 'foot_length')) {
                        $itemData['foot_length'] = $noteVal;
                    }
                }

                $minVal = 0.0;
                if (Schema::hasColumn('size_charts', 'reference_min')) {
                    $rawMin = $row['reference_min'] ?? null;
                    if ($rawMin === null || $rawMin === '') {
                        preg_match('/-?\d+(?:[.,]\d+)?/', $noteVal, $m);
                        $rawMin = isset($m[0]) ? str_replace(',', '.', $m[0]) : 0;
                    }
                    $minVal = is_numeric($rawMin) ? (float) $rawMin : 0.0;
                    $itemData['reference_min'] = $minVal;
                }

                if (Schema::hasColumn('size_charts', 'reference_max')) {
                    $rawMax = $row['reference_max'] ?? null;
                    if ($rawMax !== null && $rawMax !== '' && is_numeric(str_replace(',', '.', (string) $rawMax))) {
                        $itemData['reference_max'] = (float) str_replace(',', '.', (string) $rawMax);
                    } else {
                        preg_match_all('/-?\d+(?:[.,]\d+)?/', $noteVal, $matches);
                        if (!empty($matches[0]) && count($matches[0]) >= 2) {
                            $itemData['reference_max'] = (float) str_replace(',', '.', end($matches[0]));
                        } else {
                            $itemData['reference_max'] = $minVal;
                        }
                    }
                }

                if (Schema::hasColumn('size_charts', 'sort_order')) {
                    $sortRaw = $row['sort_order'] ?? $row['sort'] ?? null;
                    $itemData['sort_order'] = ($sortRaw !== null && $sortRaw !== '') ? (int) $sortRaw : (int) $idx;
                }
                if (Schema::hasColumn('size_charts', 'created_at')) {
                    $itemData['created_at'] = now();
                }
                if (Schema::hasColumn('size_charts', 'updated_at')) {
                    $itemData['updated_at'] = now();
                }

                if (!empty($itemData)) {
                    DB::table('size_charts')->insert($itemData);
                }
            }
        }

        if ($request->headers->get('referer') && str_contains($request->headers->get('referer'), '/admin/categories')) {
            return redirect()->route('admin.categories.index', ['chart_key' => $chartKey])
                ->with('success', 'Đã lưu bảng size và cập nhật bảng xem trước.');
        }

        return redirect()->route('admin.size-charts.index')
            ->with('success', 'Cập nhật bảng size thành công.');
    }

    public function list(): View
    {
        $editId = request()->route('id') ?? request()->route('category');
        if ($editId !== null && request()->routeIs('*.edit')) {
            $category = Category::findOrFail((int) $editId);

            return view('admin.categories.edit', compact('category'));
        }

        $categories = Category::query()->latest('id')->get();
        $groups = Schema::hasTable('size_chart_groups')
            ? DB::table('size_chart_groups')->orderBy(Schema::hasColumn('size_chart_groups', 'id') ? 'id' : 'chart_key')->get()
            : collect();

        $selectedKey = request('chart_key')
            ?? (data_get($groups->first(), 'chart_key') ?? data_get($groups->first(), 'key') ?? 'shoe_vn');

        $selectedGroup = $groups->first(function ($g) use ($selectedKey) {
            return (string) (data_get($g, 'chart_key') ?? data_get($g, 'key')) === (string) $selectedKey;
        }) ?? $groups->first();

        $sizeRows = collect();
        if (Schema::hasTable('size_charts') && $selectedKey) {
            $itemKeyCol = Schema::hasColumn('size_charts', 'chart_key')
                ? 'chart_key'
                : (Schema::hasColumn('size_charts', 'group_key') ? 'group_key' : null);

            if ($itemKeyCol !== null) {
                $q = DB::table('size_charts')->where($itemKeyCol, $selectedKey);
                if (Schema::hasColumn('size_charts', 'sort_order')) {
                    $q->orderBy('sort_order');
                } elseif (Schema::hasColumn('size_charts', 'id')) {
                    $q->orderBy('id');
                }
                $sizeRows = $q->get()->map(function ($r, $i) {
                    return [
                        'size' => $r->size_label ?? $r->size ?? $r->name ?? '',
                        'range' => $r->reference_value ?? $r->note ?? $r->foot_length ?? '',
                        'sort' => $r->sort_order ?? ($i + 1),
                    ];
                });
            }
        }

        if ($selectedGroup && $sizeRows->isNotEmpty()) {
            $selectedGroup = (object) array_merge((array) $selectedGroup, ['rows' => $sizeRows->all()]);
        }

        $viewName = view()->exists('admin.categories.index')
            ? 'admin.categories.index'
            : (view()->exists('admin.categories.list') ? 'admin.categories.list' : 'admin.categories.index');

        return view($viewName, compact('categories', 'groups', 'selectedGroup', 'selectedKey'));
    }
}