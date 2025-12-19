import { useState } from 'react'
import { TextInput } from '@/utils/components/ui/form/TextInput'
import { Button } from '@/utils/components/ui/button'

export const AddShowWhen = ({ defaultValue = [], onChange }) => {
    const [items, setItems] = useState(Array.isArray(defaultValue) ? defaultValue : [])

    // 項目追加
    const addItem = () => {
        const newItems = [...items, { field_id: '', value: '' }]
        setItems(newItems)
        onChange && onChange(newItems)
    }

    // 項目削除
    const removeItem = (index) => {
        const newItems = items.filter((_, i) => i !== index)
        setItems(newItems)
        onChange && onChange(newItems)
    }

    // 項目編集
    const updateItem = (index, key, val) => {
        const newItems = items.map((item, i) => (i === index ? { ...item, [key]: val } : item))
        setItems(newItems)
        onChange && onChange(newItems)
    }

    return (
        <>
            <div>
                {items.map((item, index) => (
                    <div className="flex gap-2 mb-2 items-center" key={index}>
                        <TextInput
                            defaultValue={item.field_id}
                            placeholder="対象フィールドID"
                            onChange={(value, e) => updateItem(index, 'field_id', value)}
                        />
                        <TextInput
                            defaultValue={item.value}
                            placeholder="値"
                            onChange={(value, e) => updateItem(index, 'value', value)}
                        />
                        <Button size="sm" color="red" outline onClick={() => removeItem(index)}>
                            削除
                        </Button>
                    </div>
                ))}
                <Button size="sm" outline onClick={addItem}>
                    ＋選択肢を追加
                </Button>
            </div>
        </>
    )
}
