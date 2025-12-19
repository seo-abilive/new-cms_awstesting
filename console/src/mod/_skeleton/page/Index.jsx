import { config } from '../utils/config'
import { ResourceIndex } from '@/utils/components/common/ResourceIndex'

export const Index = () => {
    const breads = [{ name: config.name }]
    const columns = [
        { key: 'title', label: 'タイトル' },
        { key: 'actions', label: '', sortable: false, _props: { style: { width: '10%' } } },
    ]

    return (
        <>
            <ResourceIndex options={{ breads, config, columns }} />
        </>
    )
}
