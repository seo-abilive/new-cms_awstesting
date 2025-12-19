import { NextResponse } from 'next/server'
import { cookies, headers } from 'next/headers'

export async function POST(request) {
    try {
        const body = await request.json()

        console.log('POST received:', {
            hasBody: !!body,
            bodyKeys: body ? Object.keys(body) : [],
            hasTitle: !!body?.title,
            hasPublicDate: !!body?.public_date,
            hasCustomBlock: !!body?.custom_block,
        })

        // データが空の場合は空のレスポンスを返す（demoと同じ動作）
        if (!body || Object.keys(body).length === 0) {
            console.log('Empty body, returning empty response')
            return new NextResponse('', {
                status: 200,
                headers: {
                    'Content-Type': 'text/html; charset=utf-8',
                },
            })
        }

        // プレビューデータを取得（toFlatFrontArray()で変換されたデータをそのまま使用）
        // bodyには既にtitle, public_date, custom_blockなどが含まれている
        const previewData = body

        // プレビューデータをCookieに保存（1時間有効）
        const cookieStore = await cookies()
        const dataString = JSON.stringify(previewData)

        console.log('Saving to cookie:', {
            dataSize: dataString.length,
            hasTitle: !!previewData.title,
            hasPublicDate: !!previewData.public_date,
        })

        cookieStore.set('preview_data', dataString, {
            httpOnly: true,
            secure: process.env.NODE_ENV === 'production',
            sameSite: 'lax',
            maxAge: 3600, // 1時間
            path: '/', // すべてのパスで読み取れるようにする
        })

        // return NextResponse.json({ success: true })

        // news/previewページにリダイレクト
        // headersから適切にURLを構築
        const headersList = await headers()
        const host = headersList.get('host')
        const protocol = headersList.get('x-forwarded-proto') || 'http'
        const redirectUrl = `${protocol}://${host}/news/preview`

        const response = NextResponse.redirect(redirectUrl, {
            status: 303, // POSTからGETへのリダイレクトなので303を使用
        })

        // Cookieをリダイレクトレスポンスにも設定
        response.cookies.set('preview_data', dataString, {
            httpOnly: true,
            secure: process.env.NODE_ENV === 'production',
            sameSite: 'lax',
            maxAge: 3600,
            path: '/',
        })

        return response
    } catch (error) {
        console.error('Preview POST Error:', error)
        return NextResponse.json({ success: false, error: error.message }, { status: 400 })
    }
}
