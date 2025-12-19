import { createContext, useContext, useEffect, useMemo, useState } from 'react'
import { useAxios } from '@/utils/hooks/useAxios'

const AuthContext = createContext(null)

export const AuthProvider = ({ children }) => {
    const { sendRequest } = useAxios()
    const [user, setUser] = useState(null)
    const [loading, setLoading] = useState(true)

    const fetchMe = async () => {
        setLoading(true)
        try {
            const result = await sendRequest({ method: 'GET', url: 'me' })
            if (result?.success) {
                setUser(result.data)
            } else {
                setUser(null)
            }
            return result
        } catch (err) {
            // 401/419エラーなど、認証失敗時は user をクリア
            setUser(null)
            return { success: false, error: err }
        } finally {
            setLoading(false)
        }
    }

    useEffect(() => {
        fetchMe()
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [])

    const value = useMemo(() => ({ user, setUser, loading, refresh: fetchMe }), [user, loading])

    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

export const useAuth = () => {
    const ctx = useContext(AuthContext)
    if (!ctx) throw new Error('useAuth must be used within AuthProvider')
    return ctx
}
