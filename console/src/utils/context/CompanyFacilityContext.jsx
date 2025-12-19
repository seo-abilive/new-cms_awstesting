import { createContext, useContext, useEffect, useMemo, useState } from 'react'
import { useLocation, useParams } from 'react-router'
import { useAxios } from '@/utils/hooks/useAxios'
import { useNavigation } from '@/utils/hooks/useNavigation'
import { useAuth } from '@/utils/context/AuthContext'
import { USER_TYPE, MASTER_FACILITY_ALIAS } from '@/core/user/utils/config'

const CompanyFacilityContext = createContext(undefined)

export const useCompanyFacility = () => {
    const ctx = useContext(CompanyFacilityContext)
    if (ctx === undefined) {
        return {
            company: null,
            facilities: [],
            companyName: '',
            facilityOptions: [],
            selectedFacilityAlias: MASTER_FACILITY_ALIAS,
            setFacilityAndNavigate: () => {},
            baseDashboardRoot: '/manage/',
            baseContentRoot: '/manage/',
            isAiUse: false,
        }
    }
    return ctx
}

export const CompanyFacilityProvider = ({ adminPanel, children }) => {
    const location = useLocation()
    const params = useParams()
    const { sendRequest } = useAxios()
    const { navigateTo } = useNavigation()
    const { user } = useAuth()

    const [company, setCompany] = useState(null)
    const [facilities, setFacilities] = useState([])
    const [selectedFacilityAlias, setSelectedFacilityAlias] = useState(MASTER_FACILITY_ALIAS)

    // derive aliases from URL
    const { company_alias, facility_alias } = params

    // Fetch company and facilities when path changes under /manage
    useEffect(() => {
        const run = async () => {
            if (!company_alias) {
                setCompany(null)
                setFacilities([])
                setSelectedFacilityAlias(MASTER_FACILITY_ALIAS)
                return
            }

            const companyRes = await sendRequest({
                method: 'get',
                url: `/contract/company/resource?criteria[alias]=${company_alias}`,
            })
            const companyData = companyRes?.data?.payload?.data?.[0] || null
            setCompany(companyData)

            if (companyData?.id) {
                const facilityRes = await sendRequest({
                    method: 'get',
                    url: `/contract/facility/resource?criteria[company_id]=${companyData.id}`,
                })
                const list = facilityRes?.data?.payload?.data || []
                // 施設ユーザーは許可された施設のみ
                if (user?.user_type === USER_TYPE.FACILITY) {
                    const allowedSet = new Set(
                        (user?.facilities || [])
                            .filter((f) => f?.company?.alias === company_alias)
                            .map((f) => f?.alias)
                            .filter(Boolean)
                    )
                    setFacilities(list.filter((f) => allowedSet.has(f?.alias)))
                } else {
                    setFacilities(list)
                }
            } else {
                setFacilities([])
            }

            // 企業スタッフ（company）は master のみ許可
            if (user?.user_type === USER_TYPE.COMPANY) {
                if (facility_alias !== MASTER_FACILITY_ALIAS) {
                    // master以外にアクセスしようとした場合はmasterにリダイレクト
                    navigateTo(`/${adminPanel}/${company_alias}/${MASTER_FACILITY_ALIAS}/`)
                    return
                }
                setSelectedFacilityAlias(MASTER_FACILITY_ALIAS)
            } else if (user?.user_type === USER_TYPE.FACILITY) {
                // facility ユーザーは master を選べないため、初期選択を許可内に丸める
                const current = facility_alias
                const allowedSet = new Set(
                    (user?.facilities || [])
                        .filter((f) => f?.company?.alias === company_alias)
                        .map((f) => f?.alias)
                        .filter(Boolean)
                )
                const next =
                    current && allowedSet.has(current) ? current : Array.from(allowedSet)[0]
                setSelectedFacilityAlias(next)
            } else {
                setSelectedFacilityAlias(facility_alias || MASTER_FACILITY_ALIAS)
            }
        }
        run()
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [company_alias, facility_alias])

    const facilityOptions = useMemo(() => {
        const options = facilities.map((f) => ({ label: f.facility_name, value: f.alias }))
        // 企業スタッフ（company）の場合はmasterのみ
        if (user?.user_type === USER_TYPE.COMPANY) {
            return [{ label: '企業（master）', value: MASTER_FACILITY_ALIAS }]
        }
        // 施設スタッフ（facility）の場合は施設のみ
        if (user?.user_type === USER_TYPE.FACILITY) {
            return options
        }
        // manage（および master）なら master を選択肢に含める
        return [{ label: '企業（master）', value: MASTER_FACILITY_ALIAS }, ...options]
    }, [facilities, user?.user_type])

    const baseDashboardRoot = useMemo(() => {
        if (!company_alias) return `/${adminPanel}/`
        const fa = selectedFacilityAlias || MASTER_FACILITY_ALIAS
        return `/${adminPanel}/${company_alias}/${fa}/`
    }, [company_alias, selectedFacilityAlias])

    const baseContentRoot = useMemo(() => {
        if (!company_alias) return `/${adminPanel}/`
        const fa = selectedFacilityAlias || MASTER_FACILITY_ALIAS
        return `/${adminPanel}/${company_alias}/${fa}/`
    }, [company_alias, selectedFacilityAlias])

    const setFacilityAndNavigate = (alias) => {
        setSelectedFacilityAlias(alias || MASTER_FACILITY_ALIAS)
        if (!company_alias) return

        // masterパネルの場合はcontent/modelを追加
        let suffix = adminPanel === 'master' ? 'content/model' : ''

        if (!alias || alias === MASTER_FACILITY_ALIAS) {
            navigateTo(`/${adminPanel}/${company_alias}/${MASTER_FACILITY_ALIAS}/${suffix}`)
        } else {
            navigateTo(`/${adminPanel}/${company_alias}/${alias}/${suffix}`)
        }
    }

    // パスを置換
    const replacePath = (path) => {
        return path
            .replace(':company_alias', company_alias || '')
            .replace(':facility_alias', facility_alias || '')
    }

    // パンくずを取得
    const getBreads = (append = []) => {
        const breads = [{ name: company?.company_name }, ...append]
        return breads
    }

    // AI使用可否
    const isAiUse = useMemo(() => {
        const currentPath = window.location.pathname
        const hasApiKey = company?.ai_api_key && company?.ai_api_key.trim() !== ''
        const isManage = currentPath.includes('manage')
        return hasApiKey && isManage
    }, [company])

    const value = {
        company,
        facilities,
        companyName: company?.company_name,
        facilityOptions,
        selectedFacilityAlias,
        setFacilityAndNavigate,
        baseDashboardRoot,
        baseContentRoot,
        company_alias,
        facility_alias,
        replacePath,
        getBreads,
        isAiUse,
    }

    return (
        <CompanyFacilityContext.Provider value={value}>{children}</CompanyFacilityContext.Provider>
    )
}
