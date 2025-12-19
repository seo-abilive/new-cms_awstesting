import { http, HttpResponse } from 'msw'
import config from '../config/configLoader'
import contentModelData from './data/content_model'

export const handlers = [
    http.options('*', () => {
        return new HttpResponse(null, {
            status: 200,
            headers: {
                'Access-Control-Allow-Origin': '*', // 本番環境ではより厳密なオリジンを指定してください
                'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS',
                'Access-Control-Allow-Headers': 'Content-Type, X-Requested-With, Authorization',
                'Access-Control-Allow-Credentials': 'true',
            },
        })
    }),

    http.get(config.endpointUrl + 'action_log', () => {
        return new HttpResponse(
            {
                title: 'aaa',
            },
            { status: 200 }
        )
    }),
]
