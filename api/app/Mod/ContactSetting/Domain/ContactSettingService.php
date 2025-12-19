<?php

namespace App\Mod\ContactSetting\Domain;

use App\Domain\BaseService;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Validator;
use App\Mod\ContactSetting\Domain\Models\ContactSetting;
use App\Mod\ContentField\Domain\Models\ContentField;
use App\Services\Validation\FieldValidationService;
use App\Core\User\Domain\PermissionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

/**
 * @property ContactSetting $model
 * @property FieldValidationService $validate
 */
class ContactSettingService extends BaseService
{
    protected $validate;

    public function __construct(ContactSetting $model, FieldValidationService $validate)
    {
        parent::__construct($model);
        $this->validate = $validate;
    }

    public function findList(Request $request, ?int $limit = null, array $with = [], string $flatMethod = 'toFlatArray'): array
    {
        if (!$limit) {
            $limit = config('contact_setting.list.limit');
        }

        // ミドルウェアで権限チェック済み
        return parent::findList($request, $limit, $with, $flatMethod);
    }

    /**
     * トークンから設定情報を取得
     */
    public function findDetailFromToken(Request $request, string $token, string $flatMethod = 'toFlatArray'): mixed
    {
        $this->model->setHidden(['recaptcha_key']);
        $post = $this->model->where('token', $token)->first();
        if (!$post) {
            throw new \Exception('Contact setting not found');
        }
        return method_exists($post, $flatMethod) && $this->isFlat ? $post->{$flatMethod}() : $post;
    }

    public function findDetail(Request $request, mixed $id, array $with = [], string $flatMethod = 'toFlatArray'): mixed
    {
        // 権限チェックのため、まずオブジェクトとして取得
        $post = $this->model::where(function ($query) use ($request) {
            $criteria = $request->get('criteria', []);
            $this->appendCriteria($criteria, $query);
        })->with($with)->findOrFail($id);

        // created_by / updated_by をレスポンスに含める
        $post->makeVisible(['created_by', 'updated_by']);

        // 権限スコープチェック（スコープでは対応できないため、個別にチェック）
        $this->checkPermissionScope($request, $post, 'read');

        return method_exists($post, $flatMethod) && $this->isFlat ? $post->{$flatMethod}() : $post;
    }

    /**
     * 権限スコープをチェック
     */
    protected function checkPermissionScope(Request $request, ContactSetting $post, string $permission): void
    {
        $permissionService = app(PermissionService::class);

        $permissionService->checkPermissionScopeForPost(
            $request,
            $post,
            'contact_setting',
            null, // お問い合わせ設定は機能レベルの権限
            $permission
        );
    }

    public function findOneBy(Request $request, array $with = [], string $flatMethod = 'toFlatArray'): mixed
    {
        // ミドルウェアで権限チェック済み
        return parent::findOneBy($request, $with, $flatMethod);
    }

    /**
     * 並び替え処理
     * 並び替え権限がある場合は、個別のアイテムに対する権限チェックをスキップ
     */
    public function sort(Request $request): array
    {
        return DB::transaction(function () use ($request) {
            $sortIds = $request->input('sort_ids', []);
            foreach ($sortIds as $key => $id) {
                // 並び替え処理では、findDetailを使わずに直接取得して権限チェックをスキップ
                $post = $this->model::findOrFail($id);
                $post->sort_num = ($key + 1);
                $post->save();
            }
            return ['result' => true];
        });
    }

    public function appendCriteria(?array $criteria = [], $query): void
    {
        parent::appendCriteria($criteria, $query);
    }

    public function validateRequest(Request $request, mixed $post = null): void
    {

        $rules = [
            "title" => ['required'],
            "from_address" => ['required'],
            "to_address" => ['required'],
            "subject" => ['required'],
            "body" => ['required'],
        ];
        $messages = [
            'title.required' => 'タイトルは必須項目です。',
            'from_address.required' => '送信者アドレスは必須項目です。',
            'to_address.required' => '送信先アドレスは必須項目です。',
            'subject.required' => '件名は必須項目です。',
            'body.required' => '本文は必須項目です。',
        ];

        if ($request->request->get('is_return')) {
            $rules['return_field'] = ['required'];
            $rules['return_subject'] = ['required'];
            $rules['return_body'] = ['required'];
            $messages['return_field.required'] = '返信フィールドは必須項目です。';
            $messages['return_subject.required'] = '返信件名は必須項目です。';
            $messages['return_body.required'] = '返信本文は必須項目です。';
        }

        if ($request->request->get('is_recaptcha')) {
            $rules['recaptcha_site_key'] = ['required'];
            $rules['recaptcha_secret_key'] = ['required'];
            $messages['recaptcha_site_key.required'] = 'reCAPTCHAサイトキーは必須項目です。';
            $messages['recaptcha_secret_key.required'] = 'reCAPTCHAシークレットキーは必須項目です。';
        }

        $validator = Validator::make($request->request->all(), $rules, $messages);
        $validator->validate();
    }

    public function beforeSave(Request $request, ContactSetting $post, array &$inputs): void
    {
        // 企業と施設の情報を設定
        $companyAndFacility = $this->findCompanyAndFacility($request);
        $company = $companyAndFacility['company'];
        $facility = $companyAndFacility['facility'];

        // 更新時のみスコープチェック
        if ($post->id) {
            $this->checkPermissionScope($request, $post, 'write');
        }

        $inputs['company_id'] = $company->id;
        $inputs['assignable_type'] = get_class($facility);
        $inputs['assignable_id'] = $facility->id;

        // 新規作成時のみトークンを生成
        if (!$post->id) {
            // トークンの生成
            do {
                $token = Str::uuid()->toString();
            } while (ContactSetting::where('token', $token)->exists());

            $inputs['token'] = $token;
        } else {
            // 更新時のみスコープチェック
            $this->checkPermissionScope($request, $post, 'write');
        }
    }

    public function delete(Request $request, mixed $id = null): array
    {
        $post = $this->findDetail($request, $id);

        // 権限スコープチェック
        if ($post instanceof ContactSetting) {
            $this->checkPermissionScope($request, $post, 'delete');
        }

        $post->delete();

        return [
            'id' => $post->id,
            'result' => true
        ];
    }

    /**
     * reCAPTCHAの検証
     */
    protected function verifyRecaptcha(string $token, string $secretKey): bool
    {
        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
            ]);

            $result = $response->json();

            // スコアが0.5以上の場合は有効とみなす（v3は0.0〜1.0のスコアを返す）
            return isset($result['success']) && $result['success'] === true && isset($result['score']) && $result['score'] >= 0.5;
        } catch (\Exception $e) {
            // エラーが発生した場合は検証失敗として扱う
            return false;
        }
    }

    /**
     * メール送信
     */
    public function storeSendMail(Request $request, string $token): array
    {
        $post = $this->findDetailFromToken($request, $token);
        if (!$post) {
            throw new \Exception('Contact setting not found');
        }

        $rules = [];
        $message = [];

        $inputs = $request->request->all();

        foreach ($post->fields as $field) {
            $this->validate->addValidationRules(new ContentField($field), $rules, $message, null, $inputs);
        }

        // バリデーション
        $validator = Validator::make($inputs, $rules, $message);
        $validator->validate();

        if ($request->query->get('validate_only', 0)) {
            return ['success' => true];
        }

        // reCAPTCHAが有効な場合は検証
        if ($post->is_recaptcha) {
            $recaptchaToken = $inputs['recaptcha_token'] ?? null;

            if (!$recaptchaToken) {
                throw new \Exception('reCAPTCHAトークンが提供されていません');
            }

            $isValid = $this->verifyRecaptcha($recaptchaToken, $post->recaptcha_secret_key);

            if (!$isValid) {
                throw new \Exception('reCAPTCHAの検証に失敗しました');
            }
        }

        // メール送信
        $to = $post->to_address;
        $from = $post->from_address;
        $fromName = $post->from_name;
        $subject = $this->replaceBody($post->subject, $inputs, $post);
        $body = $this->replaceBody($post->body, $inputs, $post);
        $this->sendMail($to, $from, $fromName, $subject, $body);

        // 施設に送信
        if ($post->is_return) {
            $returnField = $post->return_field;
            $to = $inputs[$returnField];
            $returnSubject = $this->replaceBody($post->return_subject, $inputs, $post);
            $returnBody = $this->replaceBody($post->return_body, $inputs, $post);
            $this->sendMail($to, $from, $fromName, $returnSubject, $returnBody);
        }

        return ['success' => true];
    }

    /**
     * 本文を置換
     */
    protected function replaceBody(string $body, array $inputs, ContactSetting $post): string
    {
        foreach ($inputs as $key => $value) {
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }

        return $body;
    }

    protected function sendMail(string $to, string $from, string $fromName, string $subject, string $body, ?string $file = null): void
    {
        Mail::raw($body, function ($message) use ($to, $from, $fromName, $subject, $file) {
            $message->to($to)
                ->from($from, $fromName)
                ->subject($subject)
            ;

            if ($file) {
                $message->attach($file);
            }
        });
    }
}
