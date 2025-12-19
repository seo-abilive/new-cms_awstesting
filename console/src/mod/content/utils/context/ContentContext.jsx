import { createContext, useContext, useEffect, useState } from 'react'
import { useParams } from 'react-router'
import { useAxios } from '@/utils/hooks/useAxios'
import { config as modelConfig } from '@/mod/content_model/utils/config'
import { config } from '../config'
import { getUrlParams } from '@/utils/common'
import { useNavigation } from '@/utils/hooks/useNavigation'
import { Spinner } from '@/utils/components/ui/spinner'
import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'

const ContentContext = createContext(undefined)

export const useContent = () => {
    const context = useContext(ContentContext)
    if (context === undefined) {
        throw new Error('useContet must be used within a ContentProvider')
    }

    return context
}

export const ContentContextProvider = ({ children }) => {
    const { company_alias, facility_alias, replacePath } = useCompanyFacility()
    const { model_name } = useParams()
    const { loading, sendRequest } = useAxios()
    const { navigateTo } = useNavigation()
    const [listConfig, setListConfig] = useState(null)

    // Content Model 情報取得
    const [modelData, setModelData] = useState(null)
    const fetchModelData = async () => {
        const response = await sendRequest({
            method: 'get',
            url:
                `${replacePath(`:company_alias/:facility_alias/content/model`)}/find?` +
                getUrlParams({ criteria: { alias: model_name } }),
        })

        // データなし
        if (!response?.data.payload.data) {
            navigateTo('/')
        } else {
            let data = response.data.payload.data

            // config設定変更
            config.name = data.title
            config.path = `/manage/${company_alias}/${facility_alias}/${data.alias}`
            config.end_point = `${company_alias}/${facility_alias}/content/${data.alias}`

            getListConfig(data)
            setModelData(data)
        }
    }

    useEffect(() => {
        fetchModelData()
    }, [model_name])

    // カテゴリ用コンフィグ取得
    const getCateConfig = () => {
        const cateConfig = { ...config }
        cateConfig.parent_path = config.path
        cateConfig.path = `${
            config.path +
            '/category'
                .replace(':company_alias', company_alias)
                .replace(':facility_alias', facility_alias)
        }`
        cateConfig.end_point = config.end_point + '/category'

        return cateConfig
    }

    // 一覧表示設定取得
    const getListConfig = (data) => {
        const newColumns = []
        const newAddScopedColumns = {}

        data?.fields?.forEach((field, key) => {
            if (field?.is_list_heading) {
                newColumns.push({ label: field?.name, key: field?.field_id })
            }

            switch (field?.field_type) {
                case 'radio':
                case 'select':
                    newAddScopedColumns[field?.field_id] = (item, row, idx) => (
                        <td key={idx} className="p-2">
                            {item[field?.field_id]?.label}
                        </td>
                    )
                    break
                case 'checkbox':
                    newAddScopedColumns[field?.field_id] = (item, row, idx) => (
                        <td key={idx} className="p-2">
                            {item[field?.field_id]?.label}
                        </td>
                    )
                    break
                case 'media_image':
                    newAddScopedColumns[field?.field_id] = (item, row, idx) => (
                        <td key={idx} className="p-2">
                            <img src={item[field?.field_id]['file_url']} className="w-70" />
                        </td>
                    )
                    break
            }
        })

        setListConfig({ columns: newColumns, addScopedColumns: newAddScopedColumns })
    }

    return (
        <ContentContext.Provider
            value={{
                config,
                modelData,
                loading,
                getCateConfig,
                listConfig,
                refreshModelData: fetchModelData,
            }}
        >
            {loading || !modelData ? <Spinner /> : <>{children}</>}
        </ContentContext.Provider>
    )
}
