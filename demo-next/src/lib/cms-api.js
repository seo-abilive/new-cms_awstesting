// lib/cms-api.js
const CMS_BASE_URL = process.env.CMS_API_URL || 'http://new-cms-api/api/v1'

export async function getContentList(modelName, options = {}) {
    try {
        const params = new URLSearchParams({
            mode: options.mode || 'list',
            limit: options.limit || 10,
            current: options.page || options.current || 1, // pageまたはcurrentパラメータをサポート
        })

        // criteriaパラメータを配列形式で追加
        if (options.criteria) {
            Object.entries(options.criteria).forEach(([key, value]) => {
                if (value !== null && value !== undefined && value !== '') {
                    params.append(`criteria[${key}]`, value)
                }
            })
        }

        const response = await fetch(`${CMS_BASE_URL}/${modelName}?${params}`, {
            next: {
                revalidate: options.revalidate || 60, // ISR: 60秒で再検証
            },
        })

        if (!response.ok) {
            throw new Error(`API Error: ${response.status}`)
        }

        return await response.json()
    } catch (error) {
        console.error('CMS API Error:', error)
        return {
            success: false,
            contents: [],
            error: error.message,
        }
    }
}

export async function getContentDetail(modelName, id, options = {}) {
    try {
        const params = new URLSearchParams()

        // criteriaパラメータを配列形式で追加
        if (options.criteria) {
            Object.entries(options.criteria).forEach(([key, value]) => {
                if (value !== null && value !== undefined && value !== '') {
                    params.append(`criteria[${key}]`, value)
                }
            })
        }

        const queryString = params.toString()
        const url = queryString
            ? `${CMS_BASE_URL}/${modelName}/${id}?${queryString}`
            : `${CMS_BASE_URL}/${modelName}/${id}`

        const response = await fetch(url, {
            next: {
                revalidate: 60, // ISR: 60秒で再検証
            },
        })

        if (!response.ok) {
            throw new Error(`API Error: ${response.status}`)
        }

        return await response.json()
    } catch (error) {
        console.error('CMS API Error:', error)
        return {
            success: false,
            contents: null,
            error: error.message,
        }
    }
}

export async function getCategories(modelName) {
    try {
        const response = await fetch(`${CMS_BASE_URL}/${modelName}/categories`, {
            next: {
                revalidate: 60, // ISR: 60秒で再検証（カテゴリは更新頻度が低い）
            },
        })

        if (!response.ok) {
            throw new Error(`API Error: ${response.status}`)
        }

        return await response.json()
    } catch (error) {
        console.error('CMS API Error:', error)
        return {
            success: false,
            contents: [],
            error: error.message,
        }
    }
}

export async function getMarkupList(modelName, options = {}) {
    try {
        const params = new URLSearchParams({
            limit: options.limit || 10,
            offset: options.offset || 0,
        })

        // criteriaパラメータを配列形式で追加
        if (options.criteria) {
            Object.entries(options.criteria).forEach(([key, value]) => {
                if (value !== null && value !== undefined && value !== '') {
                    params.append(`criteria[${key}]`, value)
                }
            })
        }

        const response = await fetch(`${CMS_BASE_URL}/${modelName}/markup?${params}`, {
            next: {
                revalidate: options.revalidate || 60,
            },
        })

        if (!response.ok) {
            throw new Error(`API Error: ${response.status}`)
        }

        return await response.json()
    } catch (error) {
        console.error('CMS Markup API Error:', error)
        return {
            success: false,
            contents: [],
            error: error.message,
        }
    }
}

export async function getMarkupDetail(modelName, id, options = {}) {
    try {
        const params = new URLSearchParams()

        // criteriaパラメータを配列形式で追加
        if (options.criteria) {
            Object.entries(options.criteria).forEach(([key, value]) => {
                if (value !== null && value !== undefined && value !== '') {
                    params.append(`criteria[${key}]`, value)
                }
            })
        }

        const queryString = params.toString()
        const url = queryString
            ? `${CMS_BASE_URL}/${modelName}/markup/${id}?${queryString}`
            : `${CMS_BASE_URL}/${modelName}/markup/${id}`

        const response = await fetch(url, {
            next: {
                revalidate: 60,
            },
        })

        if (!response.ok) {
            throw new Error(`API Error: ${response.status}`)
        }

        return await response.json()
    } catch (error) {
        console.error('CMS Markup API Error:', error)
        return {
            success: false,
            contents: null,
            error: error.message,
        }
    }
}
