import { useState, useCallback, useEffect } from 'react'
import axios from 'axios'
import config from '@/config/configLoader'

export const useAxios = (baseClient = {}) => {
    const [data, setData] = useState(null)
    const [error, setError] = useState(null)
    const [loading, setLoading] = useState(false)
    const [validationErrors, setValidationErrors] = useState(null)

    const apiClient = axios.create({
        baseURL: config.endpointUrl,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        withCredentials: true,
        xsrfCookieName: 'XSRF-TOKEN',
        xsrfHeaderName: 'X-XSRF-TOKEN',
        ...baseClient,
    })

    const getCookie = (name) => {
        const value = `; ${document.cookie}`
        const parts = value.split(`; ${name}=`)
        if (parts.length === 2) return parts.pop().split(';').shift()
        return null
    }

    // リクエスト前に XSRF-TOKEN を強制的にヘッダへ設定
    apiClient.interceptors.request.use((cfg) => {
        if (!cfg.headers) cfg.headers = {}
        const xsrf = getCookie('XSRF-TOKEN')
        if (xsrf && !cfg.headers['X-XSRF-TOKEN']) {
            try {
                // Laravel 側はデコード前の値（URLエンコード解除済み）が必要
                cfg.headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrf)
            } catch (_) {
                cfg.headers['X-XSRF-TOKEN'] = xsrf
            }
        }
        return cfg
    })

    // レスポンスインターセプタ: 未認証/CSRF失効時はログインへ遷移
    apiClient.interceptors.response.use(
        (response) => response,
        (err) => {
            if (axios.isAxiosError(err) && err.response) {
                const status = err.response.status
                if (status === 401 || status === 419) {
                    const base = config.basename || '/'
                    const baseNormalized = base.endsWith('/') ? base : `${base}/`
                    const loginPath = `${baseNormalized}login`
                    const path = window.location.pathname || '/'
                    const search = window.location.search || ''
                    const isAuthPage =
                        path.startsWith(`${baseNormalized}login`) ||
                        path.startsWith(`${baseNormalized}reset-password`)
                    const isPublicWidget = path.startsWith(`${baseNormalized}widget`)
                    if (isPublicWidget) {
                        return Promise.reject(err)
                    }
                    if (!isAuthPage) {
                        try {
                            const relativePath = path.startsWith(baseNormalized)
                                ? `/${path.slice(baseNormalized.length)}`
                                : path
                            localStorage.setItem('postLoginRedirect', relativePath + search)
                        } catch (_) {}
                    }
                    if (path !== loginPath) {
                        window.location.href = loginPath
                    }
                }
            }
            return Promise.reject(err)
        }
    )

    const sendRequest = useCallback(async (config) => {
        const abortController = new AbortController()
        config.signal = abortController.signal

        setLoading(true)
        setError(null)
        setData(null)

        try {
            const response = await apiClient.request(config)
            setData(response.data)
            return { success: true, data: response.data }
        } catch (err) {
            if (axios.isCancel(err)) {
                console.log('Request canceled', err.message)
                return { success: false, error: err, cancelled: true }
            }
            setError(err)

            if (axios.isAxiosError(err) && err.response) {
                if (err.response.status === 422) {
                    const fieldErrors = err.response.data.errors || {}
                    setValidationErrors(fieldErrors)
                    return { success: false, error: err, validationErrors: fieldErrors }
                } else {
                    setValidationErrors(null)
                }
            }
            return { success: false, error: err }
        } finally {
            setLoading(false)
        }
    }, [])

    useEffect(() => {
        const abortController = new AbortController()
        return () => {
            abortController.abort()
        }
    }, [])

    return { data, error, loading, validationErrors, sendRequest }
}
