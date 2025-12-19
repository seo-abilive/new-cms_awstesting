import { Navigate } from 'react-router-dom'
import { useAuth } from '@/utils/context/AuthContext'
import { USER_TYPE } from '@/core/user/utils/config'

export const RootRedirect = () => {
    const { user, loading } = useAuth()

    if (loading) return null

    if (!user) {
        return <Navigate to="/login" replace />
    }

    const userType = user?.user_type
    const firstCompanyAlias = user?.companies?.[0]?.alias
    const firstFacilityAlias = user?.facilities?.[0]?.alias
    const facilityCompanyAlias = user?.facilities?.[0]?.company?.alias || firstCompanyAlias
    let to = '/master/'
    if (userType === USER_TYPE.COMPANY && firstCompanyAlias) {
        to = `/manage/${firstCompanyAlias}/master/`
    } else if (userType === USER_TYPE.MANAGE && firstCompanyAlias) {
        to = `/manage/${firstCompanyAlias}/master/`
    } else if (userType === USER_TYPE.FACILITY && facilityCompanyAlias && firstFacilityAlias) {
        to = `/manage/${facilityCompanyAlias}/${firstFacilityAlias}/`
    } else if (userType === USER_TYPE.MANAGE) {
        to = '/manage/'
    }
    return <Navigate to={to} replace />
}
