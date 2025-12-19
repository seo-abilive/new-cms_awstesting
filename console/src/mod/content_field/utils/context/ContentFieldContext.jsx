import { createContext, useContext, useEffect } from 'react'
import { useParams } from 'react-router'
import { useAxios } from '@/utils/hooks/useAxios'
import { Spinner } from '@/utils/components/ui/spinner'
import { config as modelConfig } from '@/mod/content_model/utils/config'
import { config } from '../config'
import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'
const ContentFieldContext = createContext(undefined)

export const useContetField = () => {
    const context = useContext(ContentFieldContext)
    if (context === undefined) {
        throw new Error('useContetField must be used within a ContentFieldProvider')
    }
    return context
}

export const ContentFieldProvider = ({ children }) => {
    const { getBreads: getBreadsCompanyFacility, replacePath: replacePathCompanyFacility } =
        useCompanyFacility()
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

    const replacePath = (path) => {
        return replacePathCompanyFacility(path).replace(':model_id', model_id)
    }

    return (
        <ContentFieldContext.Provider
            value={{
                model_id,
                getBreads,
                replacePath,
                config: {
                    ...config,
                    path: replacePath(config.path),
                    end_point: replacePath(config.end_point),
                },
            }}
        >
            {loading && <Spinner />}
            {!loading && <>{children}</>}
        </ContentFieldContext.Provider>
    )
}
