import { createContext, useContext, useState, useEffect } from 'react'
import { useLocation } from 'react-router'
import { config } from '../config'

const ContractFacilityContext = createContext(undefined)
const STORAGE_KEY = 'facility_filter_company'

export const useContractFacility = () => {
    const context = useContext(ContractFacilityContext)
    if (context === undefined) {
        throw new Error('useContractFacility must be used within a ContractFacilityProvider')
    }
    return context
}

export const ContractFacilityProvider = ({ children }) => {
    let clone = { ...config }
    clone.name = '施設管理'
    clone.path = config.path + '/facility'
    clone.end_point = config.end_point + '/facility'

    const location = useLocation()
    const { company, remove_company = false } = location.state || {}

    // localStorageから保存されたcompanyを読み込む
    const [savedCompany, setSavedCompany] = useState(() => {
        try {
            const stored = localStorage.getItem(STORAGE_KEY)
            return stored ? JSON.parse(stored) : null
        } catch (e) {
            return null
        }
    })

    // companyパラメータがあったらlocalStorageに保存
    useEffect(() => {
        if (company) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(company))
            setSavedCompany(company)
        }
    }, [company])

    // remove_companyパラメータがあったらlocalStorageを削除
    useEffect(() => {
        if (remove_company) {
            localStorage.removeItem(STORAGE_KEY)
            setSavedCompany(null)
        }
    }, [remove_company])

    // 使用するcompanyを決定
    const companyToUse = company || savedCompany

    // パンくずリストを生成（企業名がある場合は追加）
    const getBreads = (additionalBreads = [], options = {}) => {
        const breadList = [{ name: clone.name, path: options.showPath ? clone.path : undefined }]
        if (companyToUse && companyToUse.company_name) {
            breadList.push({ name: companyToUse.company_name })
        }
        return [...breadList, ...additionalBreads]
    }

    // baseParamsを計算（companyパラメータまたはlocalStorageから読み込んだcompany_idを使用）
    const getBaseParams = () => {
        if (companyToUse && companyToUse.id) {
            return { criteria: { company_id: companyToUse.id } }
        }
        return {}
    }

    const value = {
        config: clone,
        company: companyToUse,
        getBreads,
        getBaseParams,
    }

    return (
        <ContractFacilityContext.Provider value={value}>
            {children}
        </ContractFacilityContext.Provider>
    )
}
