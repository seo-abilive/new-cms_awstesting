// 環境変数からAPI URLを取得（Terraformで設定される）
const apiOrigin = import.meta.env.VITE_API_ORIGIN || 'http://new-cms-main-alb-1834578746.ap-northeast-1.elb.amazonaws.com/api/'

export default {
    endpointUrl: `${apiOrigin}admin/`,
    frontEndpointUrl: `${apiOrigin}v1/`,
    basename: '/console/dist/',
}
