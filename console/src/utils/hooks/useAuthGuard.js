import { useEffect, useState } from 'react'
import { useAxios } from '@/utils/hooks/useAxios'
import { useNavigation } from '@/utils/hooks/useNavigation'

export const useAuthGuard = () => {
    const { sendRequest } = useAxios()
    const { navigateTo } = useNavigation()
    const [user, setUser] = useState(null)
    const [loading, setLoading] = useState(true)

    useEffect(() => {
        let isMounted = true
        const run = async () => {
            setLoading(true)
            const result = await sendRequest({ method: 'GET', url: 'me' })
            if (!isMounted) return

            if (result?.success) {
                setUser(result.data)
            } else {
                navigateTo('/login')
            }
            setLoading(false)
        }
        run()
        return () => {
            isMounted = false
        }
    }, [])

    return { user, loading }
}
