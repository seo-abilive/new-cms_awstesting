import config from '@/config/configLoader'
import { ResourceSort } from '@/utils/components/common/ResourceSort'

export const Sort = () => {
    const breads = [{ name: config.name }, { name: '並び替え' }]
    const columns = [{ key: 'title', label: 'タイトル' }]

    return <ResourceSort options={{ breads, columns, config }} />
}
