import { useMemo } from 'react'
import Select from 'react-select'
import CreatabelSelect from 'react-select/creatable'
import { useTheme } from '@/utils/context/ThemeContext'

const SELECT_ALL_OPTION = { value: '__select_all__', label: '全てを選択' }

export const RSelect = ({
    isCreatable = true,
    isMulti,
    options = [],
    onChange,
    value,
    ...props
}) => {
    const { theme } = useTheme()
    const isDarkMode = theme === 'dark'

    // 複数選択の場合、「全てを選択」オプションを追加
    const enhancedOptions = useMemo(() => {
        if (!isMulti || !options || options.length === 0) {
            return options
        }
        return [SELECT_ALL_OPTION, ...options]
    }, [isMulti, options])

    // 全てが選択されているかチェック
    const isAllSelected = useMemo(() => {
        if (!isMulti || !value || !Array.isArray(value) || !options || options.length === 0) {
            return false
        }
        const selectedValues = value.map((item) => item.value || item)
        return options.every((option) => selectedValues.includes(option.value))
    }, [isMulti, value, options])

    // onChange ハンドラーを拡張
    const handleChange = (selectedOptions, actionMeta) => {
        if (!isMulti) {
            onChange?.(selectedOptions, actionMeta)
            return
        }

        if (!selectedOptions || !Array.isArray(selectedOptions)) {
            onChange?.(selectedOptions, actionMeta)
            return
        }

        // 「全てを選択」オプションを除外した実際の選択値
        const actualSelectedOptions = selectedOptions.filter(
            (option) => (option.value || option) !== SELECT_ALL_OPTION.value
        )

        // 「全てを選択」がクリックされたかチェック
        const wasSelectAllClicked = selectedOptions.some(
            (option) => (option.value || option) === SELECT_ALL_OPTION.value
        )

        if (wasSelectAllClicked) {
            // 「全てを選択」がクリックされた場合
            if (isAllSelected) {
                // 既に全て選択されている場合は、全てを解除
                onChange?.([], actionMeta)
            } else {
                // 全てを選択
                onChange?.(options, actionMeta)
            }
        } else {
            // 通常のオプションが変更された場合
            // 個別のオプションが外された場合も正しく処理
            onChange?.(actualSelectedOptions, actionMeta)
        }
    }

    // 表示用のvalue（「全てを選択」は表示に含めない）
    const enhancedValue = useMemo(() => {
        if (!isMulti) {
            return value
        }

        if (!value || !Array.isArray(value)) {
            return value
        }

        // 「全てを選択」オプションを除外して表示
        return value.filter((item) => (item.value || item) !== SELECT_ALL_OPTION.value)
    }, [isMulti, value])

    const customStyles = {
        control: (provided, state) => ({
            ...provided,
            backgroundColor: isDarkMode ? '#374151' : '#F9FAFB',
            borderColor: state.isFocused
                ? isDarkMode
                    ? '#3B82F6'
                    : '#3B82F6'
                : isDarkMode
                ? '#4B5563'
                : '#D1D5DB',
            color: isDarkMode ? 'white' : 'black',
            minHeight: '40px',
            '&:hover': {
                borderColor: isDarkMode ? '#2563EB' : '#2563EB',
            },
        }),
        menu: (provided) => ({
            ...provided,
            backgroundColor: isDarkMode ? '#374151' : '#F3F4F6',
            color: isDarkMode ? 'white' : 'rgb(16, 24, 40)',
        }),
        option: (provided, state) => ({
            ...provided,
            backgroundColor: state.isFocused
                ? isDarkMode
                    ? '#2563EB'
                    : '#3B82F6'
                : isDarkMode && state.isSelected
                ? '#1E40AF'
                : !isDarkMode && state.isSelected
                ? '#3B82F6'
                : isDarkMode
                ? '#374151'
                : 'white',
            color: state.isSelected
                ? isDarkMode
                    ? 'white'
                    : 'white'
                : state.isFocused
                ? isDarkMode
                    ? 'white'
                    : 'white'
                : isDarkMode
                ? '#D1D5DB'
                : !state.isSelected
                ? 'rgb(16, 24, 40)'
                : 'white',
            cursor: 'pointer',
            fontWeight: state.data?.value === SELECT_ALL_OPTION.value ? 'bold' : 'normal',
        }),
        singleValue: (provided) => ({
            ...provided,
            color: isDarkMode ? '#E5E7EB' : 'rgb(16, 24, 40)',
        }),
        input: (provided) => ({
            ...provided,
            color: isDarkMode ? '#E5E7EB' : 'rgb(16, 24, 40)',
        }),
        placeholder: (provided) => ({
            ...provided,
            color: isDarkMode ? '#9CA3AF' : '#6B7280',
        }),
    }

    // 「全てを選択」オプションが選択状態として表示されないようにする
    const isOptionSelected = useMemo(() => {
        return (option, selectValue) => {
            if (!isMulti) {
                return false
            }
            // 「全てを選択」オプションは選択状態として表示しない
            if ((option.value || option) === SELECT_ALL_OPTION.value) {
                return false
            }
            // enhancedValue と比較して選択状態をチェック
            if (!enhancedValue || !Array.isArray(enhancedValue)) {
                return false
            }
            return enhancedValue.some((item) => (item.value || item) === (option.value || option))
        }
    }, [isMulti, enhancedValue])

    const selectProps = {
        styles: customStyles,
        classNamePrefix: 'rs',
        isMulti,
        options: enhancedOptions,
        onChange: handleChange,
        value: enhancedValue,
        isOptionSelected,
        ...props,
    }

    return (
        <>
            {isCreatable ? (
                <CreatabelSelect
                    isClearable
                    formatCreateLabel={(inputValue) => `"${inputValue}"を作成する`}
                    {...selectProps}
                />
            ) : (
                <Select {...selectProps} />
            )}
        </>
    )
}
