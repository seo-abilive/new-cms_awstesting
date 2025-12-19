import { useAuth } from '@/utils/context/AuthContext'
import { useLocation, Navigate } from 'react-router-dom'
import { USER_TYPE } from '@/core/user/utils/config'

const isAllowed = (pathname, user) => {
    const userType = user?.user_type
    if (userType === USER_TYPE.MASTER) return true
    const segments = (pathname || '/').split('/').filter(Boolean)
    // 期待: /manage/:company_alias/(master|:facility_alias)/...
    if (segments[0] !== 'manage') return false
    const companyAlias = segments[1]
    const facilityAlias = segments[2] // 'master' または 施設alias

    if (userType === USER_TYPE.COMPANY) {
        // 企業ユーザーは企業管理画面（master）のみ許可
        const allowedCompany = user?.companies?.[0]?.alias
        if (!allowedCompany) return false
        if (companyAlias !== allowedCompany) return false
        // masterのみ許可（施設管理画面にはアクセス不可）
        return facilityAlias === 'master'
    }

    if (userType === USER_TYPE.MANAGE) {
        const allowedCompany = user?.companies?.[0]?.alias
        if (!allowedCompany) return false
        if (companyAlias !== allowedCompany) return false
        // master または 任意の施設を許可
        return true
    }

    if (userType === USER_TYPE.FACILITY) {
        // 自施設のみ許可
        const allowedPairs = (user?.facilities || []).map((f) => ({
            facility: f?.alias,
            company: f?.company?.alias,
        }))
        const match = allowedPairs.some((p) => p.company === companyAlias && p.facility === facilityAlias)
        return match
    }
    return false
}

const fallbackPath = (user) => {
    const userType = user?.user_type
    if (userType === USER_TYPE.MASTER) return '/master/'
    if (userType === USER_TYPE.COMPANY) {
        const company = user?.companies?.[0]?.alias
        return company ? `/manage/${company}/master/` : '/login'
    }
    if (userType === USER_TYPE.MANAGE) {
        const company = user?.companies?.[0]?.alias
        return company ? `/manage/${company}/master/` : '/manage/'
    }
    if (userType === USER_TYPE.FACILITY) {
        const company = user?.facilities?.[0]?.company?.alias
        const facility = user?.facilities?.[0]?.alias
        if (company && facility) return `/manage/${company}/${facility}/`
        return '/login'
    }
    return '/login'
}

export const RoleGuard = ({ children }) => {
    const { user, loading } = useAuth()
    const location = useLocation()

    if (loading) return null
    if (!user) return <Navigate to="/login" replace state={{ from: location }} />

    const ok = isAllowed(location.pathname || '/', user)
    if (!ok) {
        return <Navigate to={fallbackPath(user)} replace />
    }

    return children
}
