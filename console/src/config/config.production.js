// 環境変数からAPI URLを取得（Terraformで設定される）
// 末尾スラッシュの有無に依存しないよう正規化（effective_api_url が .../api のため）
const apiOrigin = (import.meta.env.VITE_API_ORIGIN || 'http://new-cms-main-alb-1834578746.ap-northeast-1.elb.amazonaws.com/api/').replace(/\/?$/, '')

export default {
    endpointUrl: `${apiOrigin}/admin/`,
    frontEndpointUrl: `${apiOrigin}/v1/`,
    basename: '/console/dist/',
}
