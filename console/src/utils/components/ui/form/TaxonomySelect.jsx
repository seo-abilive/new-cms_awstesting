import React, { useEffect, useState } from 'react'
import { RSelect } from './ReactSelect'
import { useAxios } from '@/utils/hooks/useAxios'
import { Spinner } from '@/utils/components/ui/spinner'

/**
 * TaxonomySelect component fetches options from an API endpoint and renders a react-select dropdown.
 *
 * @param {object} props - Props passed to the component.
 * @param {string} props.endpoint - API endpoint URL to fetch options from.
 * @param {string} [props.keyLabel='title'] - Object key to use for option labels.
 * @param {string} [props.keyValue='id'] - Object key to use for option values.
 * @returns {JSX.Element} A spinner while loading and a react-select dropdown after data is loaded.
 */
export const TaxonomySelect = ({
    defaultValue = [],
    endpoint,
    keyLabel = 'title',
    keyValue = 'id',
    ...props
}) => {
    const { data, sendRequest } = useAxios()
    const [loading, setLoading] = useState(false)
    const [options, setOptions] = useState([])

    useEffect(() => {
        if (endpoint) {
            setLoading(true)
            sendRequest({ url: endpoint, method: 'GET' })
        }
    }, [endpoint])

    useEffect(() => {
        if (data && Array.isArray(data?.payload.data)) {
            setOptions(
                data.payload.data.map((item) => ({
                    label: item[keyLabel],
                    value: item[keyValue],
                }))
            )
            setLoading(false)
        }
    }, [data])

    return (
        <>
            {loading && <Spinner />}
            {!loading && <RSelect value={defaultValue} options={options} {...props} />}
        </>
    )
}
