// 環境変数からAPI URLを取得（Terraformで設定される）
// 末尾スラッシュを除いて正規化し、/admin/ /v1/ との結合で /api/admin/ /api/v1/ になるようにする（apiadmin 404 回避）
const apiOrigin = (import.meta.env.VITE_API_ORIGIN || 'http://new-cms-main-alb-1834578746.ap-northeast-1.elb.amazonaws.com/api/').replace(/\/?$/, '')

export default {
    endpointUrl: `${apiOrigin}/admin/`,
    frontEndpointUrl: `${apiOrigin}/v1/`,
    basename: '/console/dist/',
}
