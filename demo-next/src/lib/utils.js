// lib/utils.js
export async function getSearchParams(searchParams) {
    // searchParamsがPromiseの場合は解決する
    const resolvedParams = await searchParams

    const result = {
        page: parseInt(resolvedParams?.page) || 1,
        limit: parseInt(resolvedParams?.limit) || 10,
        category: resolvedParams?.category || null,
        // 他のフィルターパラメータも追加可能
        search: resolvedParams?.search || null,
        sort: resolvedParams?.sort || null,
    }

    return result
}

// URLパラメータを構築する関数
export function buildUrlParams(params) {
    const urlParams = new URLSearchParams()

    Object.entries(params).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== '') {
            urlParams.append(key, value)
        }
    })

    return urlParams.toString()
}

// ページネーション用のURLを生成する関数
export function buildPaginationUrl(baseUrl, currentParams, page) {
    const params = { ...currentParams, page }
    const queryString = buildUrlParams(params)
    return queryString ? `${baseUrl}?${queryString}` : baseUrl
}
