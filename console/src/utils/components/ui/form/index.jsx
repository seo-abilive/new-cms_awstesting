import { Label as FLabel } from 'flowbite-react'
import React, { useEffect, useState } from 'react'
import { ReactEditor } from '@/utils/components/ui/form/ReactEditor'
import FileUploader, { MediaImage, MediaFile, MediaImages } from '@/utils/components/ui/form/Media'
import Choices from '@/utils/components/ui/form/Choices'
import { List } from '@/utils/components/ui/form/List'
import { Table } from '@/utils/components/ui/form/Table'
import { AddFields } from '@/utils/components/ui/form/AddFields'
import { CustomField } from '@/utils/components/ui/form/CustomField'
import { TextInput } from '@/utils/components/ui/form/TextInput'
import { Textarea } from '@/utils/components/ui/form/Textarea'
import { Select } from '@/utils/components/ui/form/Select'
import { Radio } from '@/utils/components/ui/form/Radio'
import { Checkbox } from '@/utils/components/ui/form/Checkbox'
import { Switch } from '@/utils/components/ui/form/Switch'
import { TaxonomySelect } from '@/utils/components/ui/form/TaxonomySelect'
import { DatePicker } from '@/utils/components/ui/form/DataPicker'
import { NumberInput } from '@/utils/components/ui/form/NumberInput'
import { CustomBlock } from '@/utils/components/ui/form/CustomBlock'
import { AddShowWhen } from '@/utils/components/ui/form/AddShowWhen'
import { AddValidation } from '@/utils/components/ui/form/AddValidation'
import { ContentReference } from './ContentReference'
import { CodeEditor } from './CodeEditor'
import { PermissionSettings } from './PermissionSettings'

/**
 * Form component wrapping an HTML form element.
 * @param {object} props - Props passed to the form element.
 */
export const Form = ({ children, ...props }) => {
    return <form {...props}>{children}</form>
}

/**
 * FormGroup component for grouping form elements with margin.
 * @param {object} props - Props passed to the div element.
 */
export const FormGroup = ({ children, ...props }) => {
    return (
        <div className="mb-4" {...props}>
            {children}
        </div>
    )
}

/**
 * Label component wrapping flowbite-react's Label.
 * @param {object} props - Props including htmlFor and children passed to FLabel.
 */
export const Label = ({ htmlFor = '', children, ...props }) => {
    return (
        <div className="mb-2 block">
            <FLabel htmlFor={htmlFor} {...props}>
                {children}
            </FLabel>
        </div>
    )
}

/**
 * FormLabel component wrapping flowbite-react's Label to display a simple label.
 * @param {object} props - Props passed to FLabel.
 * @param {string} props.label - The text content of the label.
 * @returns {JSX.Element}
 */
export const FormLabel = ({ defaultValue, label, ...props }) => {
    return <FLabel>{label || defaultValue}</FLabel>
}

/**
 * FormBuilder component dynamically renders form input components based on formType.
 * Supports 'text' (TextInput), 'textarea' (Textarea), 'select' (Select), and 'switch' (Switch).
 * @param {object} props - Props passed to the rendered component.
 * @param {string} formType - Type of form input to render.
 */
export const FormBuilder = ({
    formType = 'text',
    validationErrors = {},
    validationKey = null,
    component = () => {
        return <></>
    },
    ...props
}) => {
    switch (formType) {
        case 'textarea':
            return <Textarea {...props} />
        case 'select':
            return <Select {...props} />
        case 'radio':
            return <Radio {...props} />
        case 'checkbox':
            return <Checkbox {...props} />
        case 'switch':
            return <Switch {...props} />
        case 'label':
            return <FormLabel {...props} />
        case 'taxonomy_select':
            return <TaxonomySelect {...props} />
        case 'date':
            return <DatePicker {...props} />
        case 'richtext':
            return <ReactEditor {...props} />
        case 'number':
            return <NumberInput {...props} />
        case 'uploader':
            return <FileUploader {...props} />
        case 'component':
            return component(props)
        case 'media_image':
            return <MediaImage {...props} />
        case 'media_file':
            return <MediaFile {...props} />
        case 'media_image_multi':
            return <MediaImages {...props} />
        case 'add_choices':
            return <Choices {...props} />
        case 'list':
            return <List {...props} />
        case 'table':
            return <Table {...props} />
        case 'add_fields':
            return <AddFields {...props} />
        case 'custom_field':
            return (
                <CustomField
                    validationErrors={validationErrors}
                    validationKey={validationKey}
                    {...props}
                />
            )
        case 'custom_block':
            return (
                <CustomBlock
                    validationErrors={validationErrors}
                    validationKey={validationKey}
                    {...props}
                />
            )
        case 'add_show_when':
            return <AddShowWhen {...props} />
        case 'add_validates':
            return <AddValidation {...props} />
        case 'content_reference':
            return <ContentReference {...props} />
        case 'code_editor':
            return <CodeEditor {...props} />
        case 'permission_settings':
            return <PermissionSettings {...props} />
        default:
            return <TextInput {...props} />
    }
}

export const FormComp = ({
    item,
    defaultValue,
    validationErrors,
    onChange,
    index,
    inputs = {},
    readonly = false,
}) => {
    const { title, required = false, onFetch, show_when = [], validationKey = null, ...rest } = item
    const [show, setShow] = useState(true)

    useEffect(() => {
        if (show_when && show_when.length > 0) {
            setShow(show_when.some((when) => inputs[when.field_id] === when.value))
        }
    }, [inputs])

    const formId = item.id

    if (item.formType === 'hidden') {
        return <div key={index} className="hidden"></div>
    }

    return (
        <div key={index} className={show ? '' : 'hidden'}>
            <FormGroup>
                <Label htmlFor={formId}>
                    {title}
                    {required && <span className="text-red-600">*</span>}
                </Label>
                <FormBuilder
                    id={formId}
                    defaultValue={defaultValue}
                    onChange={(value, e) => {
                        onChange && onChange(value, e)
                    }}
                    validationErrors={validationErrors}
                    validationKey={validationKey}
                    readonly={readonly}
                    disabled={readonly}
                    {...rest}
                />
                {item?.help_text && (
                    <div className="mt-2 text-xs text-gray-500 dark:text-gray-100">
                        {typeof item.help_text === 'string'
                            ? item.help_text
                                  .split('\n')
                                  .map((line, idx) => (idx === 0 ? line : [<br key={idx} />, line]))
                            : item.help_text}
                    </div>
                )}
                {(validationErrors?.[validationKey ?? formId] || item?.error) && (
                    <p className="mt-2 text-sm text-red-600">
                        {item?.error ||
                            validationErrors[validationKey ?? formId]?.[0] ||
                            validationErrors[validationKey ?? formId]}
                    </p>
                )}
            </FormGroup>
        </div>
    )
}
