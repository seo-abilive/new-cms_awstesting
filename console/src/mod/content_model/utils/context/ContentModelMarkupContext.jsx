import { createContext, useContext, useEffect } from 'react'
import { useParams } from 'react-router'
import { useAxios } from '@/utils/hooks/useAxios'
import { config as modelConfig, markupConfig } from '@/mod/content_model/utils/config'
import { Spinner } from '@/utils/components/ui/spinner'
import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'

const ContentModelMarkupContext = createContext(undefined)

export const useContentModelMarkup = () => {
    const context = useContext(ContentModelMarkupContext)
    if (context === undefined) {
        throw new Error('useContentModelMarkup must be used within a ContentModelMarkupProvider')
    }
    return context
}

export const ContentModelMarkupProvider = ({ children }) => {
    const { getBreads: getBreadsCompanyFacility, replacePath } = useCompanyFacility()

    const { model_id } = useParams()
    const { data, loading, sendRequest } = useAxios()

    // Content Model 情報取得
    useEffect(() => {
        ;(async () => {
            await sendRequest({
                method: 'get',
                url: `${replacePath(modelConfig.end_point)}/${model_id}`,
            })
        })()
    }, [model_id])

    const getBreads = (append = []) => {
        const breads = getBreadsCompanyFacility([
            { name: modelConfig.name, path: replacePath(modelConfig.path) },
            { name: data?.payload.data.title },
            ...append,
        ])

        return breads
    }

    const value = {
        model_id,
        getBreads,
        config: (() => {
            let clone = { ...markupConfig }
            clone.path = replacePath(clone.path).replace(':model_id', model_id)
            clone.end_point = replacePath(clone.end_point)
            return clone
        })(),
        replacePath,
    }
    return (
        <ContentModelMarkupContext.Provider value={value}>
            {loading && <Spinner />}
            {!loading && <>{children}</>}
        </ContentModelMarkupContext.Provider>
    )
}
